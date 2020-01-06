<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Feedback
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name = '';

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email = '';

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $message = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Feedback
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Feedback
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Feedback
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
