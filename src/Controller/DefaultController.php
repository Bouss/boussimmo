<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/homepage", methods={"GET"}, options={"expose"=true}, name="homepage")
     *
     * @return Response
     */
    public function homepage(): Response
    {
        return $this->render('default/_homepage.html.twig');
    }
}
