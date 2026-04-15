<?php

namespace Modules\Seo\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Services\BaseCrudService;
use Modules\Cms\Models\Content;
use Modules\Seo\Enums\permissions\SeoPermissions;
use Modules\Seo\Models\Seo;
use Modules\Seo\Models\SeoStaticPage;
use Yajra\DataTables\Facades\DataTables;

class SeoService extends BaseCrudService
{
    protected $modelClass = Seo::class;

    protected $modelScopes = [
        'model',
    ];

    protected $unnecessaryFieldsForCrud = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'robots',
        'canonical_url',
        'page_target',
    ];

    public function createModel(array $data): Seo
    {
        $subject = $this->resolvePageTarget($data['page_target']);

        $translations = $this->createTranslations($data, 'meta_title', [
            'meta_description',
            'meta_keywords',
            'og_title',
            'og_description',
            'og_image',
            'robots',
            'canonical_url',
        ]);

        return DB::transaction(function () use ($subject, $translations) {
            $seo = new Seo;
            $seo->model()->associate($subject);
            $seo->save();
            if (! empty($translations)) {
                $seo->update($translations);
            }

            return $seo->fresh(['translations', 'model']);
        });
    }

    public function updateModel(Seo $model, array $data): Seo
    {
        DB::transaction(function () use ($model, $data) {
            $this->updateTranslations($model, $data, 'meta_title', [
                'meta_description',
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image',
                'robots',
                'canonical_url',
            ]);
        });

        return $model->fresh(['translations', 'model']);
    }

    public function getDataTable(array $data): JsonResponse
    {
        $model = Seo::query()->with('model');

        if ($this->shouldShowTrash($data, SeoPermissions::VIEW_TRASH)) {
            $model = $model->onlyTrashed();
        }

        return DataTables::of($model)
            ->filter(function ($query) use ($data) {
                if (isset($data['search']['value']) && ! empty($data['search']['value'])) {
                    $query->simpleSearch($data['search']['value']);
                }
            })
            ->addColumn('target', fn (Seo $seo) => $seo->adminTargetLabel())
            ->addColumn('meta_title', fn (Seo $seo) => $seo->smartTrans('meta_title') ?? '—')
            ->addColumn('actions', function (Seo $seo) {
                return app('customDataTable')
                    ->routePrefix('seo.entries')
                    ->of($seo, SeoPermissions::PERMISSION_NAMESPACE)
                    ->excludeActions([VIEW_ACTION])
                    ->getDatatableActions();
            })
            ->toJson();
    }

    /**
     * @return array<int, array{id: string, text: string}>
     */
    public function getPageTargetOptionsForForm(): array
    {
        $out = [];

        foreach (SeoStaticPage::query()->orderBy('sort_order')->get() as $page) {
            $out[] = [
                'id' => SeoStaticPage::class.'|'.$page->getKey(),
                'text' => $page->adminLabel(),
            ];
        }

        // $query = Content::query()
        //     ->whereIn('type', [BaseContentTypes::PAGES, BaseContentTypes::BLOGS, BaseContentTypes::FAQS])
        //     ->orderBy('id');

        // foreach ($query->get() as $content) {
        //     $out[] = [
        //         'id' => Content::class.'|'.$content->getKey(),
        //         'text' => $content->smartTrans('title').' ('.$content->type.')',
        //     ];
        // }

        return $out;
    }

    /**
     * Resolve a validated `page_target` string (ClassName|id) from the create form.
     */
    public function resolvePageTarget(string $pageTarget): SeoStaticPage|Content
    {
        [$class, $id] = explode('|', $pageTarget, 2);

        return $class::query()->findOrFail($id);
    }

    /**
     * Public meta row for a registered static page key (seo_static_pages), merged with optional seo_entries row.
     *
     * @return array<string, string|null>|null Null if the static page key is unknown.
     */
    public function publicMetaByStaticPageKey(string $key): ?array
    {
        $page = SeoStaticPage::query()->where('key', $key)->first();

        if (! $page) {
            return null;
        }

        $seo = Seo::query()
            ->where('model_type', $page->getMorphClass())
            ->where('model_id', $page->getKey())
            ->first();

        return $this->buildPublicPayload($seo, $page)['meta'];
    }

    /**
     * Public meta for CMS content (pages, blogs, faqs) with optional seo_entries row.
     *
     * @return array<string, string|null>
     */
    public function publicMetaForContent(Content $content): array
    {
        $seo = Seo::query()
            ->where('model_type', $content->getMorphClass())
            ->where('model_id', $content->getKey())
            ->first();

        return $this->buildPublicPayload($seo, $content)['meta'];
    }

    /**
     * Build merged SEO payload for public API (stored meta + fallbacks).
     *
     * @return array<string, mixed>
     */
    public function buildPublicPayload(?Seo $seo, SeoStaticPage|Content $subject, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        app()->setLocale($locale);

        $meta = $this->resolveMetaRow($seo, $subject, $locale);

        return [
            'subject' => $this->subjectSummary($subject),
            'locale' => $locale,
            'meta' => $meta,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function subjectSummary(SeoStaticPage|Content $subject): array
    {
        if ($subject instanceof SeoStaticPage) {
            return [
                'kind' => 'static',
                'key' => $subject->key,
                'path_hint' => $subject->path_hint,
            ];
        }

        return [
            'kind' => 'content',
            'content_type' => $subject->type,
            'content_id' => (string) $subject->getKey(),
            'slug' => $subject->slug,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    protected function resolveMetaRow(?Seo $seo, SeoStaticPage|Content $subject, string $locale): array
    {
        $defaults = $this->defaultMetaForSubject($subject, $locale);

        if (! $seo) {
            return $defaults;
        }

        $t = $seo->translate($locale);
        if (! $t) {
            return $defaults;
        }

        return [
            'meta_title' => $t->meta_title ?: $defaults['meta_title'],
            'meta_description' => $t->meta_description ?: $defaults['meta_description'],
            'meta_keywords' => $t->meta_keywords ?: $defaults['meta_keywords'],
            'og_title' => $t->og_title ?: $defaults['og_title'],
            'og_description' => $t->og_description ?: $defaults['og_description'],
            'og_image' => $t->og_image ?: $defaults['og_image'],
            'robots' => $t->robots ?: $defaults['robots'],
            'canonical_url' => $t->canonical_url ?: $defaults['canonical_url'],
        ];
    }

    /**
     * @return array<string, string|null>
     */
    protected function defaultMetaForSubject(SeoStaticPage|Content $subject, string $locale): array
    {
        if ($subject instanceof Content) {
            return [
                'meta_title' => $subject->translate($locale)?->title ?? $subject->smartTrans('title'),
                'meta_description' => $subject->translate($locale)?->short_description ?? $subject->smartTrans('short_description'),
                'meta_keywords' => null,
                'og_title' => $subject->translate($locale)?->title ?? $subject->smartTrans('title'),
                'og_description' => $subject->translate($locale)?->short_description ?? $subject->smartTrans('short_description'),
                'og_image' => null,
                'robots' => null,
                'canonical_url' => null,
            ];
        }

        $label = $subject->adminLabel();

        $metaDescription = match ($subject->key) {
            'provider_search' => trans('front::home.search_results_meta_description'),
            'login' => trans('front::auth.login_subtitle'),
            'provider_register' => trans('front::provider_register.landing_meta_description'),
            'provider_register_apply' => trans('front::auth.register_subtitle'),
            'provider_forgot_password' => trans('front::auth.forgot_subtitle'),
            'provider_reset_password' => trans('front::auth.reset_subtitle'),
            'provider_dashboard' => trans('front::auth.dashboard_subtitle'),
            default => null,
        };

        $metaKeywords = $subject->key === 'provider_search'
            ? trans('front::home.search_results_meta_keywords')
            : null;

        return [
            'meta_title' => $label.' | '.config('app.name'),
            'meta_description' => $metaDescription,
            'meta_keywords' => $metaKeywords,
            'og_title' => $label,
            'og_description' => $metaDescription,
            'og_image' => null,
            'robots' => null,
            'canonical_url' => null,
        ];
    }
}
