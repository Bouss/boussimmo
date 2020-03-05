<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="default")
     *
     * @return Response
     */
    public function default(): Response
    {
        return null !== $this->getUser() ?
            $this->forward('App\Controller\PropertyAdController::index') :
            $this->render('default/homepage.html.twig');
    }
}
