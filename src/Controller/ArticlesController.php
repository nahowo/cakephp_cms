<?php
// src/Controller/ArticlesController.php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Text;

class ArticlesController extends AppController
{
    public function index(): void
    {
        $this->Authorization->skipAuthorization();
        $this->paginate = [
        'order' => [
            'Articles.created' => 'desc'
        ],
        ];
        $articles = $this->paginate($this->Articles);
        $this->set(compact('articles'));
    }

    public function view(?string $slug): void
    {
        $this->Authorization->skipAuthorization();
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        $this->Authorization->authorize($article);
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            $article->user_id = $this->request->getAttribute('identity')->getIdentifier();
            $article->slug = Text::slug($article->title);
            $article->published = 1;

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));

                return $this->redirect(['action' => 'view', $article->slug]);
            }
            debug($article->getErrors());
            $this->Flash->error(__('Unable to add your article.'));
        }
        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list')->all();

        // Set tags to the view context
        $this->set('tags', $tags);

        $this->set('article', $article);
    }

    public function edit(?string $slug)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
        $this->Authorization->authorize($article);
        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                'accessibleFields' => ['user_id' => false],
            ]);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                
                return $this->redirect(['action' => 'view', $article->slug]);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list')->all();

        // Set tags to the view context
        $this->set('tags', $tags);

        $this->set('article', $article);
    }

    public function delete(?string $slug)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->Authorization->authorize($article);
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));

            return $this->redirect(['action' => 'index']);
        }
    }

    // public function tags()
    // {
    //     // The 'pass' key is provided by CakePHP and contains all
    //     // the passed URL path segments in the request.
    //     $tags = $this->request->getParam('pass');

    //     // Use the ArticlesTable to find tagged articles.
    //     $articles = $this->Articles->find('tagged', tags: $tags)
    //         ->all();

    //     // Pass variables into the view template context.
    //     $this->set([
    //         'articles' => $articles,
    //         'tags' => $tags,
    //     ]);
    // }

    public function tags(...$tags)
    {
        // Use the ArticlesTable to find tagged articles.
        $this->Authorization->skipAuthorization();
        $articles = $this->Articles->find('tagged', tags: $tags)
            ->all();

        // Pass variables into the view template context.
        $this->set([
            'articles' => $articles,
            'tags' => $tags,
        ]);
    }
}