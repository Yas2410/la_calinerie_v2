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
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @route("admin/child/show/{id}", name="admin_child_show")
     * @param ChildRepository $childRepository
     * @param UserRepository $userRepository
     * @param $id
     * @return Response
     */
    public function Child(ChildRepository $childRepository, UserRepository $userRepository, $id)
    {
        $child = $childRepository->find($id);
        $user = $userRepository->find($id);

        return $this->render('admin/children/child.html.twig', [
            'child' => $child,
            'user' => $user,
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

            $entityManager->persist($child);
            $entityManager->flush();

            $this->addFlash('success', "La fiche enfant a bien été créée !");

        }
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
        $child = $childRepository->find($id);
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

            $entityManager->persist($child);
            $entityManager->flush();

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
        $entityManager->remove($child);
        $entityManager->flush();

        return $this->render('admin/children/child_delete.html.twig', [
            'child' => $child
        ]);
    }

}