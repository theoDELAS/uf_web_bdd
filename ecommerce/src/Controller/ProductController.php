<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/{page<\d+>?1}", name="product_index")
     */
    public function index(ProductRepository $product, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Product::class)
                    ->setPage($page);

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination
        ]);
    }



    /**
     * @Route("/product/{slug}/add", name="product_add")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addPanier(Product $product, EntityManagerInterface $manager)
    {
        $panier = $this->getUser()->getPanier();
        $amount = $panier->getAmount();

        $panier->addProduct($product);
        $panier->addUser($this->getUser());
        $panier->setAmount($amount + $product->getPrice());

        $manager->persist($panier);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le jeu <strong>{$product->getTitle()}</strong> a bien été ajouté à votre panier"
        );
        return $this->redirectToRoute('product_show', [
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product_show")
     * @param Product $product
     * @return Response
     */
    public function show(Product $product, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAuthor($this->getUser());
            $comment->setProduct($product);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire concernant le jeu <strong>{$product->getTitle()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('product_show', [
                'slug' => $product->getSlug()
            ]);
        }


        return $this->render('product/show.html.twig', [
            'product' => $product,
            'category' => $product->getCategory(),
            'platforms' => $product->getPlatforms()->getValues(),
            'form' => $form->createView()
        ]);
    }
}
