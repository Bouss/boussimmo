<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/homepage")
 */class HomepageController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, options={"expose"=true}, name="homepage")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig');
    }
}
