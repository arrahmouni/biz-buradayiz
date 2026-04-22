<?php

namespace Modules\Front\Http\View\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Modules\Cms\Models\Content;
use Modules\Seo\Http\Services\SeoService;

class FrontLayoutMetaComposer
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function compose(View $view): void
    {
        if ($view->offsetExists('pageMeta') && $view->offsetGet('pageMeta') !== null) {
            return;
        }

        $view->with('pageMeta', $this->resolveMeta($view, Route::currentRouteName()));
    }

    /**
     * @return array<string, string|null>
     */
    protected function resolveMeta(View $view, ?string $routeName): array
    {
        if ($routeName === 'front.blog.show' && $view->offsetExists('post')) {
            $post = $view->offsetGet('post');
            if ($post instanceof Content) {
                return $this->seoService->publicMetaForContent($post);
            }
        }

        if ($routeName === 'front.page.show' && $view->offsetExists('page')) {
            $page = $view->offsetGet('page');
            if ($page instanceof Content) {
                return $this->seoService->publicMetaForContent($page);
            }
        }

        $staticKey = config('front.meta.route_to_static_key', [])[$routeName] ?? null;

        if ($staticKey) {
            $fromDb = $this->seoService->publicMetaByStaticPageKey($staticKey);

            if ($fromDb !== null) {
                return $fromDb;
            }
        }

        return $this->fallbackMetaForRoute($routeName);
    }

    /**
     * @return array<string, string|null>
     */
    protected function fallbackMetaForRoute(?string $routeName): array
    {
        $title = match ($routeName) {
            'front.contact.show' => trans('front::home.contact_page_title'),
            'front.blog.index' => trans('front::home.blog_page_title'),
            'front.page.faq' => trans('front::home.faq_page_title'),
            'front.search' => trans('seo::static_pages.provider_search').' | '.config('app.name'),
            'front.provider.login' => trans('front::auth.login_title').' | '.config('app.name'),
            'front.provider.register' => trans('front::provider_register.landing_meta_title').' | '.config('app.name'),
            'front.provider.register.form' => trans('front::auth.register_title').' | '.config('app.name'),
            'front.provider.password.request' => trans('front::auth.forgot_title').' | '.config('app.name'),
            'front.provider.password.reset' => trans('front::auth.reset_title').' | '.config('app.name'),
            'front.provider.dashboard' => trans('front::auth.dashboard_title').' | '.config('app.name'),
            default => trans('front::home.page_title'),
        };

        $description = match ($routeName) {
            'front.search' => trans('front::home.search_results_meta_description'),
            'front.provider.login' => trans('front::auth.login_subtitle'),
            'front.provider.register' => trans('front::provider_register.landing_meta_description'),
            'front.provider.register.form' => trans('front::auth.register_subtitle'),
            'front.provider.password.request' => trans('front::auth.forgot_subtitle'),
            'front.provider.password.reset' => trans('front::auth.reset_subtitle'),
            'front.provider.dashboard' => trans('front::auth.dashboard_subtitle'),
            default => trans('front::home.page_description'),
        };

        $keywords = match ($routeName) {
            'front.search' => trans('front::home.search_results_meta_keywords'),
            default => trans('front::home.page_keywords'),
        };

        return [
            'meta_title' => $title,
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'robots' => null,
            'canonical_url' => null,
        ];
    }
}
