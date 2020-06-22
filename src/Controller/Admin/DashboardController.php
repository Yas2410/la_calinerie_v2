<?php

/* Controller : "Chef d'orchestre de l'application" qui reçoit les requêtes et gère les
interactions entre les utilisateurs et le modèle. */

// NB : "App" est égal à "src" : src/Controller/DashboardController.php.
// je créé un namespace qui correspond au chemin vers cette classe.
// Cela va permettre à Symfony d'auto-charger ma classe.
namespace App\Controller\Admin;

/* Pour pouvoir utiliser la classe dans mon code,
je fais un "use" vers le namespace (qui correspond au chemin) de la classe "Route".
Cela revient à faire un import ou un require en PHP*/

use App\Repository\ChildRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @param UserRepository $userRepository
     * @param ChildRepository $childRepository
     * @return Response
     */
    public function adminDashboard(
        // Je fais appel à mes Repository User et Child puisque
        // je veux afficher la liste des derniers enfants inscrits
        // et la liste des derniers comptes Parents créés
        UserRepository $userRepository,
        ChildRepository $childRepository
    )
    {
        // Je paramètre mon affichage en indiquant que je souhaite les ID décroissants
        // et donc les plus récents, avec une limite de 5 données
        $lastUsers = $userRepository->findBy([], ['id' => 'DESC'], 5, 0);
        $lastChildren = $childRepository->findBy([], ['id' => 'DESC'], 5, 0);

        // Je souhaite afficher cela sur le tableau de bord Admin et
        // je retourne donc la vue correspondante
        return $this->render('admin/home/dashboard.html.twig', [
            'users' => $lastUsers,
            'children' => $lastChildren,
        ]);
    }
}

