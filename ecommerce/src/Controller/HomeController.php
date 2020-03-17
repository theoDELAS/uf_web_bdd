<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PlatformRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    private $categoryRepository;
    private $platformRepository;

    public function  __construct(CategoryRepository $categoryRepository, PlatformRepository $platformRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->platformRepository = $platformRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $product)
    {
        return $this->render(
            'home/index.html.twig',
            [
                'products' => $product->findBestProducts(3)
            ]
        );
    }

    public function navBar()
    {
        return $this->render('partials/navbar.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
            'platforms' => $this->platformRepository->findAll()
        ]);
    }

    public function adminNavBar()
    {
        return $this->render('admin/partials/navbar.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
            'platforms' => $this->platformRepository->findAll()
        ]);
    }
}
