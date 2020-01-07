<?php

namespace App\Form\Model;

use App\Validator\Constraints\User as UserAssert;

/**
 * @UserAssert\DifferentPassword()
 */
class Password
{
    /**
     * @var string
     * @UserAssert\CurrentPassword()
     */
    private $previousPassword;

    /**
     * @var string
     */
    private $newPassword;

    public function getPreviousPassword(): ?string
    {
        return $this->previousPassword;
    }

    public function setPreviousPassword(string $password): self
    {
        $this->previousPassword = $password;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $password): self
    {
        $this->newPassword = $password;

        return $this;
    }
}
