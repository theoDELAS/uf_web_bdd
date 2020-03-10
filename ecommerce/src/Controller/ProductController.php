<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/create", name="product_create")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();

            foreach($product->getImages() as $image) {
                $image->setProduct($product);
                $manager->persist($image);
            }

            $product->setAuthor($this->getUser());

            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$product->getTitle()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('product_show', [
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product_show")
     * @param Product $product
     * @return Response
     */
    public function show(Product $product)
    {
        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }
}
