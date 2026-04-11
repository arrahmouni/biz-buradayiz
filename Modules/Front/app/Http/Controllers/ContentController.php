<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Http\Request;
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

        return view('front::contents.show', [
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

        return view('front::contents.faq', [
            'faqs' => $faqs,
            'title' => __('front::home.faq_page_title'),
        ]);
    }

    public function BlogList(Request $request): View
    {
        $query = $this->publishedBlogsQuery();

        if ($search = trim((string) $request->input('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->whereTranslationLike('title', $like)
                    ->orWhereTranslationLike('short_description', $like);
            });
        }

        $this->data['blogs'] = $query->orderByDesc('published_at')->paginate(5)->withQueryString();
        $this->data['recentPosts'] = $this->publishedBlogsQuery()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
        $this->data['title'] = __('front::home.blog_page_title');

        return view('front::contents.blog-index', $this->data);
    }

    public function showBlog(string $slug): View
    {
        $post = Content::query()
            ->byType(BaseContentTypes::BLOGS)
            ->where(function ($q) use ($slug) {
                $q->where('sub_type', $slug)
                    ->orWhereHas('translations', function ($tq) use ($slug) {
                        $tq->where('slug', $slug);
                    });
            })
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();

        $this->data['post'] = $post;
        $this->data['recentPosts'] = $this->publishedBlogsQuery()
            ->where('contents.id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
        $this->data['title'] = $post->smartTrans('title');

        return view('front::contents.blog-show', $this->data);
    }

    private function publishedBlogsQuery()
    {
        return Content::query()
            ->byType(BaseContentTypes::BLOGS)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
