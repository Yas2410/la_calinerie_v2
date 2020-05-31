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
        UserRepository $userRepository,
        ChildRepository $childRepository
    )
    {
        $lastUsers = $userRepository->findBy([], ['id' => 'DESC'], 5, 0);
        $lastChildren = $childRepository->findBy([], ['id' => 'DESC'], 5, 0);
        return $this->render('admin/home/dashboard.html.twig', [
            'users' => $lastUsers,
            'children' => $lastChildren,
        ]);

    }


}