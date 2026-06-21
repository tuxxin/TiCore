<?php
namespace Tuxxin\TiCore\Addons\BlogCms\Controllers;

use Tuxxin\TiCore\Addons\BlogCms\Blog;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

final class BlogController
{
    private function blog(): Blog { return new Blog(); }

    /** GET /blog — published posts, newest first. */
    public function index(): Response
    {
        $blog = $this->blog();
        return Response::view('blog-cms::list', [
            'title'      => config('blog-cms')['site_name'] ?? 'Blog',
            'heading'    => 'Latest posts',
            'posts'      => $blog->publishedList(),
            'categories' => $blog->categories(),
            'category'   => null,
        ]);
    }

    /** GET /blog/{slug} — single published post (404 if missing/unpublished). */
    public function show($slug): Response
    {
        $blog = $this->blog();
        $post = $blog->findPublished((string) $slug);
        if (!$post) {
            return Response::view('blog-cms::post', [
                'title' => 'Not found',
                'post'  => null,
            ], 404);
        }
        return Response::view('blog-cms::post', [
            'title' => $post['title'],
            'post'  => $post,
            'html'  => Blog::mdToHtml((string) ($post['body'] ?? '')),
        ]);
    }

    /** GET /blog/category/{slug} — published posts in one category. */
    public function category($slug): Response
    {
        $blog = $this->blog();
        $cat  = $blog->findCategory((string) $slug);
        if (!$cat) {
            return Response::view('blog-cms::list', [
                'title'      => 'Category not found',
                'heading'    => 'Category not found',
                'posts'      => [],
                'categories' => $blog->categories(),
                'category'   => null,
            ], 404);
        }
        return Response::view('blog-cms::list', [
            'title'      => $cat['name'] . ' — ' . (config('blog-cms')['site_name'] ?? 'Blog'),
            'heading'    => 'Category: ' . $cat['name'],
            'posts'      => $blog->publishedByCategory((int) $cat['id']),
            'categories' => $blog->categories(),
            'category'   => $cat,
        ]);
    }
}
