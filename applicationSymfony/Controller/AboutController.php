<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AboutController extends AbstractController
{
    /**
     * @Route("/about/{slug}", name="app_about_page")
     */
    public function page(string $slug, UserPasswordEncoderInterface $encoder)
    {
        return $this->render(sprintf('content/%s.html.twig', $slug));
    }
}
