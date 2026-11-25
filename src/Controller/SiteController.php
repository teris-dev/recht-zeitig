<?php

declare(strict_types=1);

namespace App\Controller;

use Teris\ERecht24Bundle\ClientRegistrar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SiteController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ClientRegistrar $registrar): Response
    {
        return $this->render('home.html.twig', ['push_uri' => $registrar->pushUri('default')]);
    }

    #[Route('/impressum', name: 'imprint_de', defaults: ['type' => 'imprint', '_locale' => 'de'])]
    #[Route('/en/imprint', name: 'imprint_en', defaults: ['type' => 'imprint', '_locale' => 'en'])]
    #[Route('/datenschutz', name: 'privacy_de', defaults: ['type' => 'privacyPolicy', '_locale' => 'de'])]
    #[Route('/en/privacy-policy', name: 'privacy_en', defaults: ['type' => 'privacyPolicy', '_locale' => 'en'])]
    public function legal(string $type): Response
    {
        return $this->render('legal.html.twig', ['type' => $type]);
    }

    #[Route('/healthz', name: 'healthz')]
    public function healthz(): Response
    {
        return new Response('ok', 200, ['Content-Type' => 'text/plain']);
    }
}
