<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route("/", name: "default_index")]
    public function index(): Response
    {
        return null !== $this->getUser() ?
            $this->forward('App\Controller\PropertyController::index') :
            $this->render('homepage/index.html.twig');
    }
}
