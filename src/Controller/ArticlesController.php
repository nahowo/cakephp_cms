<?php
// src/Controller/ArticlesController.php
declare(strict_types=1);

namespace App\Controller;

class ArticlesController extends AppController
{
    public function index(): void
    {
        $articles = $this->paginate($this->Articles);
        $this->set(compact('articles'));
    }

    public function view(?string $slug): void
{
    $article = $this->Articles->findBySlug($slug)->firstOrFail();
    $this->set(compact('article'));
}
}