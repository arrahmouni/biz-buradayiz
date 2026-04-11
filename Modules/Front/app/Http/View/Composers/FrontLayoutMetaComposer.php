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
        if ($routeName === 'front.blog.show') {
            $post = $view->offsetGet('post');
            if ($post instanceof Content) {
                return $this->seoService->publicMetaForContent($post);
            }
        }

        if ($routeName === 'front.page.show') {
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
            default => trans('front::home.page_title'),
        };

        return [
            'meta_title' => $title,
            'meta_description' => $routeName === 'front.search'
                ? trans('front::home.search_results_meta_description')
                : trans('front::home.page_description'),
            'meta_keywords' => $routeName === 'front.search'
                ? trans('front::home.search_results_meta_keywords')
                : trans('front::home.page_keywords'),
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'robots' => null,
            'canonical_url' => null,
        ];
    }
}
