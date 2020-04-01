<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    /**
     * @Route("/admin/product/{page<\d+>?1}", name="admin_product_index")
     */
    public function index(ProductRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Product::class)
            ->setPage($page);

        return $this->render('admin/product/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/product/create", name="admin_product_create")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $product->setAuthor($this->getUser());

            foreach($product->getPlatforms() as $platform) {
                $platform->addProduct($product);
                $manager->persist($platform);
            }

            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$product->getTitle()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('admin_product_index', [
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('admin/product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'edition
     *
     * @Route("/admin/product/{id}/edit", name="admin_product_edit")
     *
     * @param Product $product
     * @param Request
     * @return Response
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach($product->getPlatforms() as $platform) {
                $platform->addProduct($product);
                $manager->persist($platform);
            }
            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce du jeu <strong>{$product->getTitle()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('admin_product_index', [
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/product/{id}/delete", name="admin_product_delete")
     *
     * @param Product $product
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Product $product, EntityManagerInterface $manager) {
        $manager->remove($product);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce du jeu <strong>{$product->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('admin_product_index');
    }
}
