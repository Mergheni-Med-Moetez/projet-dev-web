<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'dashboard_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // Nombre total de produits
        $nbProduits = $em->getRepository(Produit::class)->count([]);

        // Nombre total de commandes
        $nbCommandes = $em->getRepository(Commande::class)->count([]);

        // Total des ventes (somme totale sur toutes les commandes)
        $ventesTotal = $em->createQuery('SELECT SUM(c.total) FROM App\Entity\Commande c')
            ->getSingleScalarResult() ?? 0;

        // Top produit : le nom du produit commandé le plus souvent (exemple simple)
        $topProduit = $em->createQuery(
            'SELECT p.nom FROM App\Entity\Produit p 
            JOIN App\Entity\ProduitCommande pc WITH pc.produit = p
            GROUP BY p.id
            ORDER BY SUM(pc.quantite) DESC'
        )
        ->setMaxResults(1)
        ->getOneOrNullResult();

        $stats = [
            'nbProduits' => $nbProduits,
            'nbCommandes' => $nbCommandes,
            'ventesTotal' => $ventesTotal ? $ventesTotal : 0,
            'topProduit' => $topProduit ? $topProduit['nom'] : 'Aucun',
        ];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
