<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier_index")
     */
    public function index()
    {
        $panier = $this->getUser()->getPanier();

        return $this->render('panier/index.html.twig', [
            'panier' => $panier
        ]);
    }

    /**
     * Permet de supprimer un jeu de son pannier
     *
     * @Route("/panier/{id}/delete", name="panier_delete")
     *
     * @param Product $product
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Product $product, EntityManagerInterface $manager) {
        $panier = $this->getUser()->getPanier();
        $amount = $panier->getAmount();

        $panier->removeProduct($product);
        $panier->setAmount($amount - $product->getPrice());

        $manager->persist($panier);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le jeu <strong>{$product->getTitle()}</strong> a bien été supprimée de votre panier"
        );


        return $this->redirectToRoute('panier_index');
    }
}
