<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\View\View;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Http\Controllers\BaseWebController;
use Modules\Front\Http\Requests\ProviderSearchRequest;
use Modules\Zms\Models\City;
use Modules\Zms\Models\State;

class HomeController extends BaseWebController
{
    public function index()
    {
        return view('front::index');
    }

    public function search(ProviderSearchRequest $request): View
    {
        $validated = $request->validated();

        $query = User::query()
            ->where('type', UserType::ServiceProvider->value)
            ->where('status', AdminStatus::ACTIVE)
            ->whereHas('activePackageSubscription')
            ->with([
                'service.translations',
                'city.translations',
                'city.state.translations',
            ]);

        if (! empty($validated['service_id'] ?? null)) {
            $query->where('service_id', $validated['service_id']);
        }
        if (! empty($validated['city_id'] ?? null)) {
            $query->where('city_id', $validated['city_id']);
        } elseif (! empty($validated['state_id'] ?? null)) {
            $query->whereHas('city', fn ($q) => $q->where('state_id', $validated['state_id']));
        }

        $providers = $query
            ->orderByDesc('review_rating_average')
            ->orderByDesc('approved_reviews_count')
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
}
