<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    private $categoryRepository;

    public function  __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $product)
    {
        $products = $product->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products
        ]);
    }

    public function navBar()
    {
        return $this->render('partials/navbar.html.twig', [
            'categories' => $this->categoryRepository->findAll()
        ]);
    }
}
