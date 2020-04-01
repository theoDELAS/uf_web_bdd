<?php

namespace App\Controller;

use App\Entity\Historical;
use App\Entity\Panier;
use App\Entity\Product;
use App\Form\HistoricalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function delete(Request $request, Product $product, EntityManagerInterface $manager) {
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


//        return $this->redirectToRoute('panier_index');
        $request->getSession()
            ->getFlashBag()
            ->add('notice', 'success');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/panier/{id}/validate", name="panier_validate")
     */
    public function validatePanier(Request $request, EntityManagerInterface $manager)
    {
        $panier = $this->getUser()->getPanier();
        $historical = new Historical();
        $user = $this->getUser();

        $form = $this->createForm(HistoricalType::class, $historical);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // fait payer le user
            $newBalance = $user->getBalance() - $panier->getAmount();
            $user->buy($panier);
            $manager->persist($user);

            // associe l'historique a l'user
            $historical->setUser($user);
            $manager->persist($historical);

            // ajoute le prix du panier a l'historique
            $historical->setAmount($panier->getAmount());
            // ajoute chaque produit du panier dans l'historique, supprime ensuite le produit du panier, modifie le prix du panier
            foreach ($panier->getProduct()->getValues() as $product) {
                $historical->addProduct($product);
                $panier->removeProduct($product);
                $panier->setAmount($panier->getAmount() - $product->getPrice());
                $manager->persist($panier);
                $manager->persist($historical);
            }

            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commande a bien été effectuée, vous devriez recevoir un mail sous peu"
            );

            return $this->redirectToRoute('panier_index');
        }

        return $this->render('panier/validate.html.twig', [
            'panier' => $panier,
            'form' => $form->createView()
        ]);
    }
}
