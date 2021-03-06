<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\PlatformRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(ProductRepository $product, Request $request)
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if ($product->findBy(['title' => $form->getData()->getSearch()])) {
                return $this->redirectToRoute('product_show', [
                    'slug' => $form->getData()->getSearch()
                ]);
            } else {
                $this->addFlash(
                    'warning',
                    "Le jeu que vous cherchez n'existe pas"
                );
            }
        }
        return $this->render(
            'home/index.html.twig',
            [
                'products' => $product->findBestProducts(3),
                'form' =>$form->createView()
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

    public function footer()
    {
        return $this->render('partials/footer.html.twig');
    }

    public function adminNavBar()
    {
        return $this->render('admin/partials/navbar.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
            'platforms' => $this->platformRepository->findAll()
        ]);
    }
}
