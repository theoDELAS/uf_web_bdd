<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    /**
     * @Route("/admin/product", name="admin_product_index")
     */
    public function index(ProductRepository $repo)
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $repo->findAll()
        ]);
    }
}
