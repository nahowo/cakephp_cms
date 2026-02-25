<?php
declare(strict_types=1);

namespace App\Mailer;

use Cake\Mailer\Mailer;
use Cake\Queue\Mailer\QueueTrait;
use Cake\ORM\TableRegistry;

/**
 * User mailer.
 */

class UserMailer extends Mailer
{
    use QueueTrait;
    public function welcome(int $userId, string $url): void
    {
        $user = TableRegistry::getTableLocator()->get('Users')->get($userId);
        $this
            ->setTo($user->email)
            ->setSubject('Welcome to cakephp CMS!')
            ->setEmailFormat('html')
            ->setViewVars([
                'username'=> explode('@', $user->email)[0],
                'url'=> $url,
            ])
            ->viewBuilder()
            ->setTemplate('welcome');
    }
}