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
            'children' => $child,
            'user' => $user,
        ]);
    }

    /**
     * @route("admin/child/search", name="admin_search_child")
     * @param ChildRepository $childRepository
     * @param Request $request
     * @return Response
     */
    public function searchByChild(ChildRepository $childRepository, Request $request)
    {
        $search = $request->query->get('search');
        $children = $childRepository->getByWordInChild($search);

        return $this->render('admin/children/search_child.html.twig', [
            'search' => $search, 'children' => $children
        ]);
    }

    /**
     * @route("admin/child/update/{id}", name="admin_update_child")
     * @param Request $request
     * @param ChildRepository $childRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateChild(
        Request $request,
        ChildRepository $childRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $child = $childRepository->find($id);
        $formChild = $this->createForm(ChildType::class, $child);
        $formChild->handleRequest($request);
        if ($formChild->isSubmitted() && $formChild->isValid()) {
            $entityManager->persist($child);
            $entityManager->flush();

            $this->addFlash('sucess', "La fiche enfant a bien été modifiée !");
        }

        return $this->render('admin/children/update_child.html.twig', [
            'formChild'=>$formChild->createView()
        ]);
    }

    /**
     * @route("admin/child/delete/{id}", name="admin_delete_child")
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

        $this->addFlash('sucess', "La fiche enfant a bien été supprimée !");

        return $this->redirectToRoute('admin_children_list');
    }

}