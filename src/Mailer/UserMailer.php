<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\Mailer\Message;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * User mailer.
 */

class UserMailer extends Mailer
{
    public function welcome(User $user, string $url): void
    {
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