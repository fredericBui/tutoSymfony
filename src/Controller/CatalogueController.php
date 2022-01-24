<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\Produits1Type;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/catalogue")
 */
class CatalogueController extends AbstractController
{
    /**
     * @Route("/", name="catalogue_index", methods={"GET"})
     */
    public function index(ProduitsRepository $produitsRepository): Response
    {
        return $this->render('catalogue/index.html.twig', [
            'produits' => $produitsRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}", name="catalogue_show", methods={"GET"})
     */
    public function show(Produits $produit): Response
    {
        return $this->render('catalogue/show.html.twig', [
            'produit' => $produit,
        ]);
    }

}
