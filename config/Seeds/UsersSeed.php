<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * Users seed.
 */
class UsersSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/5/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'email' => 'user1@gmail.com',
                'password' => 'user1spassword',
                'created' => date('2026-02-23 00:00:00'),
                // 'modified' => date('Y-m-d H:i:s'),
            ],
            [
                'email' => 'user2@gmail.com',
                'password' => 'user2spassword',
                'created' => date('2026-02-23 00:00:00'),
                // 'modified' => date('Y-m-d H:i:s'),
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
