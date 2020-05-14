<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{


    /**
     * //* @Route("/parents/users", name="users")
     * @param UserRepository $userRepository
     * @return Response
     */

    public function users(UserRepository $userRepository)
    {

        $users = $userRepository->findAllNonAdmin();
        return $this->render('front/users/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @route("parents/user/show/{id}", name="user")
     * @param UserRepository $userRepository
     * @param $id
     * @return Response
     */
    public function user(UserRepository $userRepository, $id)
    {
        $user = $userRepository->find($id);

        return $this->render('front/users/user.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @route("parents/user/insert", name="user_insert")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertUser(Request $request,
                               EntityManagerInterface $entityManager,
                               SluggerInterface $slugger
    )
    {

        $user = new User();

        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été créé !");

        }
        return $this->render('front/users/insert_user.html.twig', [
            'formUser' => $formUser->createView()
        ]);

    }

    /**
     * @route("parents/user/update/{id}", name="user_update")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateUser(
        Request $request,
        UserRepository $userRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $user = $userRepository->find($id);

        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié !");

        }


        return $this->render('front/users/user_insert.html.twig', [
            'formUser' => $formUser->createView()
        ]);
    }


    /**
     * @route("parents/user/delete/{id}", name="user_delete")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
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
        $session = new Session();
        $session->invalidate();
        $user = $userRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->render('front/users/user_delete.html.twig', [
            'user' => $user
        ]);
    }
}

