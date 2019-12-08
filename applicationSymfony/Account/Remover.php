<?php

namespace App\Account;

use App\Entity\User;

class Remover
{
    public function cleanupUser(User &$user): void
    {
        $user->forget();
    }
}
