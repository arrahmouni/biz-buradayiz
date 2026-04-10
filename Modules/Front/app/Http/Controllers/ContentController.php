<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\View\View;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Models\Content;

class ContentController extends BaseController
{
    public function showPage(string $slug): View
    {
        $page = Content::query()
            ->byType(BaseContentTypes::PAGES)
            ->where(function ($q) use ($slug) {
                $q->where('sub_type', $slug)
                    ->orWhereHas('translations', function ($tq) use ($slug) {
                        $tq->where('slug', $slug);
                    });
            })
            ->firstOrFail();

        return view('front::pages.show', [
            'page' => $page,
            'title' => $page->smartTrans('title'),
        ]);
    }

    public function faq(): View
    {
        $faqs = Content::query()
            ->byType(BaseContentTypes::FAQS)
            ->orderBy('id')
            ->get();

        return view('front::pages.faq', [
            'faqs' => $faqs,
            'title' => __('front::home.faq_page_title'),
        ]);
    }
}
