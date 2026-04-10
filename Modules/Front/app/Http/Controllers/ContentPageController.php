<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\View\View;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Cms\Enums\contents\BaseContentTypes;
use Modules\Cms\Models\Content;

class ContentPageController extends BaseController
{
    public function show(string $slug): View
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
}
