<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    /**
     * Permet de supprimer un jeu de sa liste de favoris
     *
     * @Route("/favorite/{id}/delete", name="favorite_delete")
     *
     * @param Product $product
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $manager) {
        $favorite = $this->getUser()->getFavorite();

        $favorite->removeProduct($product);

        $manager->persist($favorite);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le jeu <strong>{$product->getTitle()}</strong> a bien été supprimée de vos favoris"
        );

        $request->getSession();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
