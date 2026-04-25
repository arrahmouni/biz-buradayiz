<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseWebController;
use Modules\Config\Constatnt;
use Modules\Front\Http\Requests\ProviderSearchRequest;
use Modules\Front\Http\Requests\StoreProviderReviewRequest;
use Modules\Front\Support\FeaturedProviderService;
use Modules\Platform\Http\Services\ReviewSubmissionService;
use Modules\Zms\Models\City;
use Modules\Zms\Models\State;

class ProviderController extends BaseWebController
{
    public function __construct(
        protected ReviewSubmissionService $reviewSubmissionService,
        protected FeaturedProviderService $featuredProviderService,
    ) {
        parent::__construct();
    }

    public function search(ProviderSearchRequest $request): View
    {
        $validated = $request->validated();

        $query = $this->publicProviderQuery();

        if (! empty($validated['service_id'] ?? null)) {
            $query->where('service_id', $validated['service_id']);
        }
        if (! empty($validated['city_id'] ?? null)) {
            $query->where('city_id', $validated['city_id']);
        } elseif (! empty($validated['state_id'] ?? null)) {
            $query->whereHas('city', fn ($q) => $q->where('state_id', $validated['state_id']));
        }

        $featuredProviders = collect();
        $featuredIds = [];
        $currentPage = (int) $request->input('page', 1);
        $featuredCount = (int) getSetting(Constatnt::FEATURED_PROVIDERS_COUNT, 3);

        if ($featuredCount > 0) {
            $featuredForExclusion = $this->featuredProviderService->getFeatured(clone $query, $featuredCount);
            $featuredIds = $featuredForExclusion->pluck('id')->all();

            if ($currentPage === 1) {
                $featuredProviders = $featuredForExclusion;
            }
        }

        if ($featuredIds !== []) {
            $query->whereNotIn('id', $featuredIds);
        }

        $providers = $query
            ->orderByDesc('ranking_score')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(12)
            ->withQueryString();

        $selectedState = null;
        $selectedCity = null;

        if (! empty($validated['city_id'] ?? null)) {
            $city = City::query()
                ->with(['state.translations', 'translations'])
                ->find($validated['city_id']);
            if ($city !== null) {
                $cityName = $city->smartTrans('name') ?? $city->native_name;
                $selectedCity = ['id' => (int) $city->id, 'name' => $cityName];
                if ($city->state !== null) {
                    $stateName = $city->state->smartTrans('name') ?? $city->state->native_name;
                    $selectedState = ['id' => (int) $city->state->id, 'name' => $stateName];
                }
            }
        } elseif (! empty($validated['state_id'] ?? null)) {
            $state = State::query()->with('translations')->find($validated['state_id']);
            if ($state !== null) {
                $stateName = $state->smartTrans('name') ?? $state->native_name;
                $selectedState = ['id' => (int) $state->id, 'name' => $stateName];
            }
        }

        $this->data['featuredProviders'] = $featuredProviders;
        $this->data['providers'] = $providers;
        $this->data['filters'] = [
            'service_id' => $validated['service_id'] ?? null,
            'state_id' => $validated['state_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
        ];
        $this->data['selectedState'] = $selectedState;
        $this->data['selectedCity'] = $selectedCity;
        $this->data['title'] = __('front::home.search_results_title');

        return view('front::search.providers', $this->data);
    }

    public function showProvider(string $provider): View
    {
        $publicUser = $this->publicProviderQuery()
            ->where('profile_slug', $provider)
            ->first();

        if ($publicUser !== null) {
            return $this->renderProviderShow($publicUser);
        }

        $viewer = auth('web')->user();
        if ($viewer
            && $viewer->type === UserType::ServiceProvider
            && $viewer->profile_slug === $provider) {
            $owner = User::query()
                ->where('type', UserType::ServiceProvider)
                ->where('status', AdminStatus::ACTIVE)
                ->whereKey($viewer->id)
                ->where('profile_slug', $provider)
                ->with([
                    'service.translations',
                    'city.translations',
                    'city.state.translations',
                ])
                ->first();

            if ($owner !== null) {
                return $this->renderProviderShow($owner, ownerPreviewWithoutActiveSubscription: true);
            }
        }

        abort(404);
    }

    public function storeProviderReview(StoreProviderReviewRequest $request, string $provider): RedirectResponse
    {
        $user = $this->findPublicProviderByProfileSlug($provider);
        $validated = $request->validated();

        try {
            $this->reviewSubmissionService->submit([
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'rating' => $validated['rating'],
                'body' => $validated['comment'] ?? null,
                'reviewer_display_name' => $validated['display_name'] ?? null,
            ]);
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        }

        return redirect()
            ->back()
            ->with('success', __('front::home.provider_detail_review_success'));
    }

    public function providerReviewsFragment(string $provider)
    {
        $user = $this->findPublicProviderByProfileSlug($provider);

        $perPage = 10;
        $reviews = $user->reviews()
            ->approved()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'html' => view('front::providers.partials.review-items', ['reviews' => $reviews])->render(),
            'next_page_url' => $reviews->nextPageUrl(),
        ]);
    }

    private function publicProviderQuery(): Builder
    {
        return User::query()
            ->where('type', UserType::ServiceProvider->value)
            ->where('status', AdminStatus::ACTIVE)
            ->whereHas('activePackageSubscription')
            ->with([
                'service.translations',
                'city.translations',
                'city.state.translations',
            ]);
    }

    private function findPublicProviderByProfileSlug(string $slug): User
    {
        return $this->publicProviderQuery()
            ->where('profile_slug', $slug)
            ->firstOrFail();
    }

    private function renderProviderShow(User $user, bool $ownerPreviewWithoutActiveSubscription = false): View
    {
        $perPage = 10;
        $reviews = $user->reviews()
            ->approved()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $this->data['provider'] = $user;
        $this->data['reviews'] = $reviews;
        $this->data['title'] = $user->full_name;
        $this->data['ownerPreviewWithoutActiveSubscription'] = $ownerPreviewWithoutActiveSubscription;

        return view('front::providers.show', $this->data);
    }
}
