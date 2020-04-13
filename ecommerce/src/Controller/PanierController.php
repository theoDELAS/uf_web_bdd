<?php

namespace App\Controller;

use App\Entity\Historical;
use App\Entity\Product;
use App\Form\HistoricalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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

        $request->getSession();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Permet de valider son panier et de payer
     * @Route("/panier/{id}/validate", name="panier_validate")
     */
    public function validatePanier(Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer)
    {

        $panier = $this->getUser()->getPanier();
        $user = $this->getUser();
        if ($panier->getProduct()->getValues()) {
            if ($user->getBalance() > $panier->getAmount()) {
                $historical = new Historical();
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

                    // envoi du mail
                    $contact = $form->getData();
                    $nbProducts = count($historical->getProducts()->getValues());
                    $codes = [];
                    $valideCaracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789*%^';
                    for ($i = 0; $i < $nbProducts; $i++) {
                        $codes[] .= '';
                        for ($a = 0; $a < 20; $a++) {
                            $codes[$i] .= $valideCaracteres[rand(0, strlen($valideCaracteres)-1)];
                        }
                    }

                    $transport = (new \Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl'))
                        ->setUsername('theo.delas@gmail.com')
                        ->setPassword('HTqgQf*iZPL$m4gTLt7U')
                    ;
                    $mailer = new \Swift_Mailer($transport);
                    $mail = $form->getData()->getMail();

                    $message = (new \Swift_Message('Nanomania - Facture et code d\'activation'))
                        ->setFrom(['theo.delas@gmail.com' => 'Nanomania'])
                        ->setTo([$mail])
                        ->setBody(
                            $this->renderView(
                                'emails/registration.html.twig',
                                [
                                    'prenom' => $this->getUser()->getFirstName(),
                                    'mail' => $this->getUser()->getEmail(),
                                    'codes' => $codes,
                                    'products' => $historical->getProducts(),
                                    'historical' => $historical
                                ]
                            ),
                            'text/html'
                        );
                    $mailer->send($message);

                    $this->addFlash(
                        'success',
                        "Votre commande a bien été effectuée, vous allez recevoir un mail d'ici quelques minutes contenant votre facture ainsi que vos code d'activation"
                    );

                    return $this->redirectToRoute('panier_index');
                }

                return $this->render('panier/validate.html.twig', [
                    'panier' => $panier,
                    'form' => $form->createView()
                ]);
            }
            else {
                $this->addFlash(
                    'danger',
                    "Vous n'avez pas assez d'argent sur votre compte pour valider votre panier."
                );
                return $this->redirectToRoute('panier_index');
            }
        } else {
            $this->addFlash(
                'danger',
                "Votre panier est vide."
            );
            return $this->redirectToRoute('panier_index');
        }
    }
}
