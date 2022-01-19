<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Repository\ProduitsRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/produits")
 */
class AdminProduitsController extends AbstractController
{
    /**
     * @Route("/", name="admin_produits_index", methods={"GET"})
     */
    public function index(ProduitsRepository $produitsRepository): Response
    {
        return $this->render('admin_produits/index.html.twig', [
            'produits' => $produitsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_produits_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $produit->setImageFilename($imageFileName);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_produits/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_produits_show", methods={"GET"})
     */
    public function show(Produits $produit): Response
    {
        return $this->render('admin_produits/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_produits_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FileUploader $fileUploader, Produits $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $produit->setImageFilename($imageFileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_produits/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_produits_delete", methods={"POST"})
     */
    public function delete(Request $request, Produits $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_produits_index', [], Response::HTTP_SEE_OTHER);
    }
}
