<?php

namespace App\Policies;

use App\GuestbookMessage;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuestbookMessagePolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, GuestbookMessage $message)
    {
        return $user->id == $message->user_id
            ? $this->allow()
            : $this->deny('Вы не владелец сообщения');
    }

    public function answer()
    {
        return true;
    }
}
