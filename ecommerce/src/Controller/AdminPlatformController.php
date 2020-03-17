<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Form\AdminPlatformType;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPlatformController extends AbstractController
{
    /**
     * @Route("/admin/platform", name="admin_platform_index")
     */
    public function index(PlatformRepository $platforms)
    {
        return $this->render('admin/platform/index.html.twig', [
            'platforms' => $platforms->findAll(),
        ]);
    }


    /**
     * @Route("/admin/platform/create", name="admin_platform_create")
     *
     * @param Request $request
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $platform = new Platform();

        $form = $this->createForm(AdminPlatformType::class, $platform);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($platform);
            $manager->flush();

            $this->addFlash(
                'success',
                "La plateforme <strong>{$platform->getName()}</strong> a bien été enregistrée"
            );
            return $this->redirectToRoute('admin_platform_index');
        }

        return $this->render('admin/platform/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/platform/{id}/delete", name="admin_platform_delete")
     *
     * @param Platform $platform
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Platform $platform, EntityManagerInterface $manager)
    {
        $manager->remove($platform);
        $manager->flush();

        $this->addFlash(
            'success',
            "La plateforme <strong>{$platform->getName()}</strong> a bien été supprimée"
        );


        return $this->redirectToRoute('admin_platform_index');
    }
}
