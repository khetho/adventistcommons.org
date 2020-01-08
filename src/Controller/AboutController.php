<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;

class AboutController extends AbstractController
{
    /**
     * @Route("/", name="app_about_nolanguage")
     */
    public function noLanguage()
    {
        return $this->redirectToRoute('app_about_home');
    }

    /**
     * @Route("/{_locale}", name="app_about_home")
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
     * @Route("/{_locale}/about/{slug}", name="app_about_page")
     * @param string $slug
     * @return Response
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
