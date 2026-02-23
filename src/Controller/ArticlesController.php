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
}