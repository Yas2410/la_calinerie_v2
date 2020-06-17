<?php

namespace App\Controller\Admin;

use App\Entity\Child;
use App\Form\ChildType;
use App\Repository\AllergenRepository;
use App\Repository\ChildRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class ChildController extends AbstractController
{
    /**
     * //* @Route("/admin/children", name="admin_children_list")
     * @param ChildRepository $childRepository
     * @param AllergenRepository $allergenRepository
     * @param UserRepository $userRepository
     * @return Response
     */

    public function children(ChildRepository $childRepository, AllergenRepository $allergenRepository,
                             UserRepository $userRepository)
    {
        $children = $childRepository->findAll();
        $allergen = $allergenRepository->findAll();
        $user = $userRepository->findAll();
        return $this->render('admin/children/children.html.twig', [
            'children' => $children,
            'allergen' => $allergen,
            'users' => $user,
        ]);
    }

    /* Création d'une nouvelle route avec une WildCArd */
    /**
     * @route("admin/child/show/{id}", name="admin_child_show")
     * @param ChildRepository $childRepository
     * @param $id
     * @return Response
     */
    /* La classe Repository me permet de selectionner la liste des enfants enregistrés
    en base de données */
    public function Child(ChildRepository $childRepository, $id)
    {
        /* Pour selectionner un enfant en particulier, je passe en paramètre de la méthode 'find()'
        l'$id, passé en paramètre de l'URL */
        $child = $childRepository->find($id);
        /* Je retourne une réponse par le biais de mon fichier twig via la méthode 'render()' */
        return $this->render('admin/children/child.html.twig', [
            'child' => $child,
        ]);
    }

    /**
     * @route("admin/child/insert", name="admin_child_insert")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertChild(Request $request,
                                EntityManagerInterface $entityManager,
                                SluggerInterface $slugger
    )
    {
        /* Je créé une nouvelle instance de Child que je mets dans une variable pour ensuite la lier à mon
        formulaire */
        $child = new Child();
        /* Je créé mon formulaire, ici "ChildType", que je lie à mon "new Child" créé précédemment */
        $formChild = $this->createForm(ChildType::class, $child);

        /* je demande à mon formulaire de gérer les données de la requête POST par le biais de la
        méthode "handleRequest" */
        $formChild->handleRequest($request);

        /* Si le formulaire a été envoyé et que les données sont valides : */
        if ($formChild->isSubmitted() && $formChild->isValid()) {

            /* Je récupère ici la valeur du fichier uploadé */
            $image = $formChild->get('image')->getData();

            /* Je vérifie qu'un fichier a bien été envoyé */
            if ($image) {

                /* Et, je récupère ici le nom de ce fichier */
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                /* Duquel j'exclus les caracètres spéciaux (slugger) */
                $safeFilename = $slugger->slug($originalFilename);
                /* J'ajoute au nom du fichier un identifiant unique */
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                /* Je déplace le fichier dans le dossier correspondant, paramétré au préalable dans
                mon fichier 'services.yaml', ici 'children_directory' */
                $image->move(
                    $this->getParameter('children_directory'),
                    $newFilename);
                /* Enfin, j'enregistre en base de données le nom du fichier uploadé */
                $child->setImage($newFilename);
            }

            $entityManager->persist($child);
            $entityManager->flush();

            /* J'ajoute un message flash, qui sera rappelé dans mon 'base.html.twig' afin de confirmer la création d'une
            nouvelle fiche enfant */
            $this->addFlash('success', "La fiche enfant a bien été créée !");
        }
        /* Création de ma vue et renvoi du formulaire créé dans ce même fichier twig */
        return $this->render('admin/children/child_insert.html.twig', [
            'formChild' => $formChild->createView()
        ]);
    }

    /**
     * @route("admin/child/search", name="admin_child_search")
     * @param ChildRepository $childRepository
     * @param Request $request
     * @return Response
     */

    public function searchByChild(ChildRepository $childRepository, Request $request)
    {
        /* J'utilise le childRepository pour appeler ma méthode 'getByWordInChild()'
        Ce dernier permet, en plus des méthodes find(), etc. de créer des méthodes
        plus spécifiques de SELECT de données en bdd */
        $search = $request->query->get('search');
        $children = $childRepository->getByWordInChild($search);

        return $this->render('admin/children/child_search.html.twig', [
            'search' => $search, 'children' => $children
        ]);
    }

    /**
     * @route("admin/child/update/{id}", name="admin_child_update")
     * @param Request $request
     * @param ChildRepository $childRepository
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateChild(
        Request $request,
        ChildRepository $childRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        /* Pour selectionner une fiche, je passe en paramètre de la méthode 'find()'
        l'$id et fait appel à la classe Repository de mon entité Child */
        $child = $childRepository->find($id);
        /* Je reprend le formulaire ayant servi à la création d'une fiche */
        $formChild = $this->createForm(ChildType::class, $child);
        $formChild->handleRequest($request);
        if ($formChild->isSubmitted() && $formChild->isValid()) {

            $image = $formChild->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('children_directory'),
                    $newFilename);
                $child->setImage($newFilename);
            }
        /* J'enregistre de nouveau les données en base de données */
            $entityManager->persist($child);
            $entityManager->flush();
        /* Comme pour la création, j'ajoute un message indiquant que la fiche à bien été modifiée */
            $this->addFlash('success', "La fiche enfant a bien été modifiée !");
        }

        return $this->render('admin/children/child_insert.html.twig', [
            'formChild' => $formChild->createView()
        ]);
    }

    /**
     * @route("admin/child/delete/{id}", name="admin_child_delete")
     * @param ChildRepository $childRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteChild(
        Request $request,
        ChildRepository $childRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $child = $childRepository->find($id);
        /* J'applique la méthode 'remove()' afin de supprimer la fiche */
        $entityManager->remove($child);
        /* Je valide la suppression */
        $entityManager->flush();
        /* Je redirige vers la page correspondante */
        return $this->render('admin/children/child_delete.html.twig', [
            'child' => $child
        ]);
    }
}