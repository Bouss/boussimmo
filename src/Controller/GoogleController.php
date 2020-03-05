<?php

namespace App\Controller;

use Google_Service_Gmail;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    /**
     * @Route("/connect/google", name="connect_google_start")
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('google')->redirect([Google_Service_Gmail::GMAIL_READONLY], []);
    }

    /**
     * @Route("/connect/google/check", name="connect_google_check")
     *
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry, SessionInterface $session)
    {
    }
}
