<?php

namespace App\Email;

use App\Entity\User;
use App\Form\Model\Feedback;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\NamedAddress;

class SenderToAdmin
{
    private $manager;
    private $sender;

    public function __construct(EntityManagerInterface $manager, Sender $sender)
    {
        $this->sender = $sender;
        $this->manager = $manager;
    }

    private function buildEmail(): TemplatedEmail
    {
        /** @var UserRepository $repo */
        $repo = $this->manager->getRepository(User::class);
        $admins = $repo->findAdmins();
        $email = (new TemplatedEmail())
            ->from(new NamedAddress('hello@adventistcommons.org', 'Adventist Commons'));
        foreach ($admins as $admin) {
            $email->addTo(new NamedAddress($admin->getEmail(), $admin->getFullName()));
        }

        return $email;
    }

    public function sendFeedback(Feedback $feedback): void
    {
        $email = $this->buildEmail()
            ->subject('ac: Feedback')
            ->textTemplate('email/admin/feedback.txt.twig')
            ->htmlTemplate('email/admin/feedback.html.twig')
            ->context([
                'heading' => 'Feedback',
                'base_url' => $this->sender->getBaseUrl(),
                'name' => $feedback->getName(),
                'sentEmail' => $feedback->getEmail(),
                'message' => $feedback->getMessage(),
            ]);
        $this->sender->send($email);
    }
}
