<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Error\LoaderError;

class AboutController extends AbstractController
{
    /**
     * @Route("/", name="app_about_home")
     */
    public function home()
    {
        $breadcrumbs = [
            [
                "label" => "Home",
                "url" => "/",
            ],
        ];
        
        return $this->render(
            'home.html.twig',
            ['breadcrumbs' => $breadcrumbs]
        );
    }
    
    /**
     * @Route("/about/{slug}", name="app_about_page")
     */
    public function page(string $slug)
    {
        try {
            return $this->render(sprintf('about/%s.html.twig', $slug));
        } catch (LoaderError $e) {
            throw new NotFoundHttpException();
        }
    }
}
