<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use Cake\Mailer\MailerAwareTrait;
use Cake\Routing\Router;
use Cake\Queue\Mailer\QueueTrait;
use Cake\I18n\DateTime;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    use MailerAwareTrait;
    use QueueTrait;

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->allowUnauthenticated(['login', 'register', 'verifyEmail']);
    }
    
    public function login()
    {
        $this->Authorization->skipAuthorization();
        $result = $this->Authentication->getResult();
        // If the user is logged in send them away.
        if ($result && $result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? [
                'controller' => 'Articles',
                'action' => 'index',
            ];
            return $this->redirect($target);
        }
        if ($this->request->is('post')) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    public function logout()
    {
        $this->Authorization->skipAuthorization();
        $this->Authentication->logout();
        return $this->redirect(['controller' => 'Articles', 'action' => 'index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();
        $query = $this->Users->find();
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Articles']);
        $this->set(compact('user'));
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        $this->Authorization->skipAuthorization();
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            $user['verified'] = false;
            $time = DateTime::now();
            $unixTime = $time->toUnixString();
            $randomKey = uniqid();
            $token = substr($unixTime, 0, 5) . $randomKey . substr($unixTime, 5);
            $user['email_token'] = $token;
            $user['email_token_generated_at'] = $time->format('Y-m-d H:i:s');
            $url = Router::url(['controller' => 'Users', 'action' => 'verifyEmail', $token], true);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                $this->getMailer('User')->push('verifyEmail', [$user->id, $url]);
                return $this->redirect(['action' => 'register']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    // Verify Email method
    public function verifyEmail($token = null)
    {
        $this->Authorization->skipAuthorization();
        if (!$token) {
            $this->Flash->error(__('Invalid or expired passkey. '));
            return $this->redirect(['action' => 'register']);
        }

        $user = $this->getUser($token);
        if (!$user) {
            $this->Flash->error(__('Invalid or expired passkey. '));
            return $this->redirect(['action' =>'register']);
        }
        $this->Users->patchEntity($user, [
            'verified' => true,
        ]);
        if ($this->Users->save($user)) {
            $url = Router::url(['controller' => 'Articles', 'action' => 'index'], true);
            $this->getMailer('User')->push('welcome', [$user->id, $url]);
            return $this->redirect(['action' => 'login']);
        }
        $this->Flash->error(__('The user could not be saved. Please, try again.'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function getUser($token): ?User
    {
        return $this->Users->find('all', conditions: ['email_token' => $token])->first();
    }
}
