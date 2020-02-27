<?php

namespace App\Controller;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/{id}", name="category_index")
     * @param Category $category
     * @return Response
     */
    public function index(Category $category)
    {
        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }
}
