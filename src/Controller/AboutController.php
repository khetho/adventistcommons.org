<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @return Response
     */
    public function page(string $slug, Request $request)
    {
        try {
            return $this->render(sprintf('about/%s.%s.html.twig', $slug, $request->getLocale()));
        } catch (LoaderError $e) {
            try {
                return $this->render(sprintf('about/%s.%s.html.twig', $slug, 'en'));
            } catch (LoaderError $e) {
                throw new NotFoundHttpException();
            }
        }
    }
}
