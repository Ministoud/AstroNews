<?php


namespace App\Notification\Recipient;


use App\Entity\User;
use Symfony\Component\Notifier\Recipient\Recipient;

class UserRecipient extends Recipient
{
    protected $user;

    public function __construct(User $user)
    {
//        parent::__construct('');
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}