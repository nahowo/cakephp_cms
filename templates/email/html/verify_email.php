<?php
/**
 * @var string $username
 * @var string $url
 */
?>
<div style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello, <?= h($username) ?>!</h2>
    <p>Thanks for signing up for cakephp CMS. </p>
    <p>Please verify your email address with the button below to get started. You will only need to do this once.</p>

     <div style="margin: 30px 0;">
        <a href="<?= $url ?>" 
           style="background-color: #d33c44; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Verify Email
        </a>
    </div>

    <hr style="border: none; border-top: 1px solid #eee; margin-top: 30px;">
    <p style="font-size: 0.8em; color: #777;">This email is read only. </p>
</div>