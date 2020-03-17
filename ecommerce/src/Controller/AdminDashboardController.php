<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, StatsService $statsService)
    {

        $stats = $statsService->getStats();

        $bestProducts = $statsService->getProductsStats('DESC');
        $worstProducts = $statsService->getProductsStats('ASC');

        $bestProducts = $manager->createQuery(
            'SELECT AVG(c.rating) as note, p.title, p.id
             FROM App\Entity\Comment c
             JOIN c.product p
             GROUP BY p
             ORDER BY note DESC'
        )->setMaxResults(5)
            ->getResult();

        $worstProducts = $manager->createQuery(
            'SELECT AVG(c.rating) as note, p.title, p.id
             FROM App\Entity\Comment c
             JOIN c.product p
             GROUP BY p
             ORDER BY note ASC'
        )->setMaxResults(5)
            ->getResult();


        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'bestProducts' => $bestProducts,
            'worstProducts' => $worstProducts
        ]);
    }
}
