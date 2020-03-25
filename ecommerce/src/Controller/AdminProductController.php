<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\PlatformRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce du jeu <strong>{$product->getTitle()}</strong> a bien été enregistrée"
            );
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
