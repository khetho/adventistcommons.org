<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard/admin", name="app_dashboard_admin")
     */
    public function admin(): Response
    {
        dump('ok');
        return new Response();
    }
}
