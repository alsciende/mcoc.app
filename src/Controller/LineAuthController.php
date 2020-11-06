<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LineAuthController extends AbstractController
{
    /**
     * @Route("/line/auth", name="line_auth")
     */
    public function index(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('line')
            ->redirect([
                'profile'
            ], [
            ]);
    }
}
