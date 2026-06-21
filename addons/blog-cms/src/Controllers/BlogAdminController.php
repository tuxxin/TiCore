<?php
namespace Tuxxin\TiCore\Addons\BlogCms\Controllers;

use Tuxxin\TiCore\Addons\BlogCms\Blog;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;
use TiCore\Core\Security;
use TiCore\Core\Logger;

final class BlogAdminController
{
    private function blog(): Blog { return new Blog(); }

    /** GET /admin/blog — list ALL posts. */
    public function index(): Response
    {
        return Response::view('blog-cms::admin-list', [
            'title' => 'Blog admin',
            'posts' => $this->blog()->all(),
        ]);
    }

    /** GET /admin/blog/new — empty create form. */
    public function create(): Response
    {
        return Response::view('blog-cms::admin-form', [
            'title'      => 'New post',
            'post'       => null,
            'categories' => $this->blog()->categories(),
            'action'     => '/admin/blog',
            'error'      => null,
        ]);
    }

    /** POST /admin/blog — create. */
    public function store(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $blog = $this->blog();
        $data = $this->payload($req, $blog);

        if (trim($data['title']) === '') {
            return Response::view('blog-cms::admin-form', [
                'title'      => 'New post',
                'post'       => $data,
                'categories' => $blog->categories(),
                'action'     => '/admin/blog',
                'error'      => 'Title is required.',
            ], 422);
        }

        $id = $blog->create($data);
        Logger::info("blog-cms: post #{$id} created — {$data['title']}");
        return Response::redirect('/admin/blog');
    }

    /** GET /admin/blog/edit/{id} — edit form. */
    public function edit($id): Response
    {
        $blog = $this->blog();
        $post = $blog->find((int) $id);
        if (!$post) return Response::make('Post not found', 404);

        return Response::view('blog-cms::admin-form', [
            'title'      => 'Edit post',
            'post'       => $post,
            'categories' => $blog->categories(),
            'action'     => '/admin/blog/update/' . (int) $id,
            'error'      => null,
        ]);
    }

    /** POST /admin/blog/update/{id} — update. */
    public function update(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $id   = (int) ($req->param('id') ?? 0);
        $blog = $this->blog();
        if (!$blog->find($id)) return Response::make('Post not found', 404);

        $data = $this->payload($req, $blog);
        if (trim($data['title']) === '') {
            $data['id'] = $id;
            return Response::view('blog-cms::admin-form', [
                'title'      => 'Edit post',
                'post'       => $data,
                'categories' => $blog->categories(),
                'action'     => '/admin/blog/update/' . $id,
                'error'      => 'Title is required.',
            ], 422);
        }

        $blog->update($id, $data);
        Logger::info("blog-cms: post #{$id} updated");
        return Response::redirect('/admin/blog');
    }

    /** POST /admin/blog/delete/{id} — delete. */
    public function delete(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $id = (int) ($req->param('id') ?? 0);
        $this->blog()->delete($id);
        Logger::info("blog-cms: post #{$id} deleted");
        return Response::redirect('/admin/blog');
    }

    /** Build a normalized data array from the request (resolves category name → id). */
    private function payload(Request $req, Blog $blog): array
    {
        $catName = trim((string) $req->input('category', ''));
        return [
            'title'       => trim((string) $req->input('title', '')),
            'slug'        => trim((string) $req->input('slug', '')),
            'body'        => (string) $req->input('body', ''),
            'excerpt'     => trim((string) $req->input('excerpt', '')),
            'status'      => (string) $req->input('status', 'draft'),
            'category_id' => $catName !== '' ? $blog->ensureCategory($catName) : null,
        ];
    }
}
