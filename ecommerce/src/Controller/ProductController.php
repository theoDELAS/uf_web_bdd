<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Search;
use App\Form\CommentType;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/{page<\d+>?1}", name="product_index")
     */
    public function index(ProductRepository $product, $page, PaginationService $pagination, Request $request)
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

        $pagination->setEntityClass(Product::class)
            ->setPage($page);

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }



    /**
     * Ajoute le produit au panier de l'user connecté
     *
     * @Route("/product/{slug}/add", name="product_add")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addPanier(Product $product, EntityManagerInterface $manager, Request $request)
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

        $request->getSession();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Ajoute le produit à la liste des favoris de l'user connecté
     *
     * @Route("/product/{id}/addFavorite", name="product_add_favorite")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addFavorite(Product $product, EntityManagerInterface $manager, Request $request)
    {
        $favorite = $this->getUser()->getFavorite();

        $favorite->addProduct($product);

        $manager->persist($favorite);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le jeu <strong>{$product->getTitle()}</strong> a bien été ajouté à votre liste de favoris"
        );
        $request->getSession()
            ->getFlashBag()
            ->add('notice', 'success');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/product/{slug}", name="product_show")
     * @param Product $product
     * @return Response
     */
    public function show(Product $product, Request $request, EntityManagerInterface $manager, UserRepository $repo)
    {
        if ($this->getUser()) {
            if ($repo->getUserProductComment($product, $this->getUser())) {
                $comment = $repo->getUserProductComment($product, $this->getUser())[0];
            } else {
                $comment = new Comment();
            }
        } else {
            $comment = new Comment();
        }


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
            'comments' => $product->getComments()->getValues(),
            'form' => $form->createView(),
            'inHistorical' => $product->getHistoricals()->getValues()
        ]);
    }
}
