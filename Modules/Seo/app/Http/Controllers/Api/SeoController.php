<?php

namespace Modules\Seo\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Models\Content;
use Modules\Seo\Http\Services\SeoService;
use Modules\Seo\Models\Seo;
use Modules\Seo\Models\SeoStaticPage;

class SeoController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function staticPage(Request $request, string $key)
    {
        $page = SeoStaticPage::query()->where('key', $key)->firstOrFail();

        $seo = Seo::query()
            ->where('model_type', $page->getMorphClass())
            ->where('model_id', $page->getKey())
            ->first();

        $payload = $this->seoService->buildPublicPayload($seo, $page, $request->query('locale'));

        return sendApiSuccessResponse(data: $payload);
    }

    public function contentBySlug(Request $request, string $type, string $slug)
    {
        if (! in_array($type, [BaseContentTypes::PAGES, BaseContentTypes::BLOGS, BaseContentTypes::FAQS], true)) {
            abort(404);
        }

        $content = Content::byType($type)->whereTranslation('slug', $slug)->firstOrFail();

        $seo = $content->seo;

        $payload = $this->seoService->buildPublicPayload($seo, $content, $request->query('locale'));

        return sendApiSuccessResponse(data: $payload);
    }
}
