<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{

    /**
     * //* @Route("/admin/users", name="admin_users_list")
     * @param UserRepository $userRepository
     * @return Response
     */

    public function users(UserRepository $userRepository)
    {

        $users = $userRepository->findAllNonAdmin();
        return $this->render('admin/users/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @route("admin/user/show/{id}", name="admin_user_show")
     * @param UserRepository $userRepository
     * @param $id
     * @return Response
     */
    public function user(UserRepository $userRepository, $id)
    {
        $user = $userRepository->find($id);

        return $this->render('admin/users/user.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @route("admin/user/search", name="admin_search_user")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    public function searchByUser(UserRepository $userRepository, Request $request)
    {
        $search = $request->query->get('search');
        $users = $userRepository->getByWordInUser($search);

        return $this->render('admin/users/search_user.html.twig', [
            'search' => $search, 'users' => $users
        ]);
    }

    /**
     * @route("admin/user/delete/{id}", name="admin_delete_user")
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteUser(
        Request $request,
       UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $user = $userRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur a bien été supprimé !");

        return $this->redirectToRoute('admin_users_list');
    }

}
