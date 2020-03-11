<?php

namespace App\Controller;

use App\Entity\Platform;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatformController extends AbstractController
{
    /**
     * @Route("/plateforme/{id}", name="platform_index")
     * @param Platform $category
     * @return Response
     */
    public function index(Platform $platform)
    {
        return $this->render('platform/index.html.twig', [
            'platform' => $platform,
        ]);
    }
}
