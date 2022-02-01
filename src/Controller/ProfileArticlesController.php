<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/articles")
 */
class ProfileArticlesController extends AbstractController
{
    /**
     * @Route("/", name="profile_articles_index", methods={"GET"})
     */
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('profile_articles/index.html.twig', [
            'articles' => $articlesRepository->findBy(['auteur'=>$this->getUser()])
        ]);
    }

    /**
     * @Route("/new", name="profile_articles_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);
        $article->setAuteur($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('profile_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile_articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="profile_articles_show", methods={"GET"})
     */
    public function show(Articles $article): Response
    {
        if($article->getAuteur()==$this->getUser()){
            return $this->render('profile_articles/show.html.twig', [
                'article' => $article,
            ]);
        }
        
        return $this->redirectToRoute('profile_articles_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/edit", name="profile_articles_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if($article->getAuteur()==$this->getUser()){
            $form = $this->createForm(ArticlesType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('profile_articles_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('profile_articles/edit.html.twig', [
                'article' => $article,
                'form' => $form,
            ]);
        }

        return $this->redirectToRoute('profile_articles_index', [], Response::HTTP_SEE_OTHER);
        
    }

    /**
     * @Route("/{id}", name="profile_articles_delete", methods={"POST"})
     */
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if($article->getAuteur()==$this->getUser()){
            if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
                $entityManager->remove($article);
                $entityManager->flush();
            }
        }
        
        return $this->redirectToRoute('profile_articles_index', [], Response::HTTP_SEE_OTHER);
    }
}
