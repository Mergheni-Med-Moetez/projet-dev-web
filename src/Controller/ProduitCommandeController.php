<?php

namespace App\Controller;

use App\Entity\ProduitCommande;
use App\Form\ProduitCommandeType;
use App\Repository\ProduitCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit/commande')]
final class ProduitCommandeController extends AbstractController
{
    #[Route(name: 'app_produit_commande_index', methods: ['GET'])]
    public function index(ProduitCommandeRepository $produitCommandeRepository): Response
    {
        return $this->render('produit_commande/index.html.twig', [
            'produit_commandes' => $produitCommandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitCommande = new ProduitCommande();
        $form = $this->createForm(ProduitCommandeType::class, $produitCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produitCommande);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_commande/new.html.twig', [
            'produit_commande' => $produitCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_commande_show', methods: ['GET'])]
    public function show(ProduitCommande $produitCommande): Response
    {
        return $this->render('produit_commande/show.html.twig', [
            'produit_commande' => $produitCommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProduitCommande $produitCommande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitCommandeType::class, $produitCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_commande/edit.html.twig', [
            'produit_commande' => $produitCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_commande_delete', methods: ['POST'])]
    public function delete(Request $request, ProduitCommande $produitCommande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produitCommande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produitCommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
