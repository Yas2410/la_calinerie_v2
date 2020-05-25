<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MenuController extends AbstractController
{
    /**
     * //* @Route("/admin/menus", name="admin_menus_list")
     * @param MenuRepository $menuRepository
     * @return Response
     */

    public function menus(MenuRepository $menuRepository)
    {
        $menus = $menuRepository->findAll();
        return $this->render('admin/menus/menus.html.twig', [
            'menus' => $menus
        ]);
    }

    /**
     * @route("admin/menu/show/{id}", name="admin_menu_show")
     * @param MenuRepository $menuRepository
     * @param $id
     * @return Response
     */
    public function menu(MenuRepository $menuRepository, $id)
    {
        $menu = $menuRepository->find($id);

        return $this->render('admin/menus/menu.html.twig', [
            'menu' => $menu
        ]);
    }

    /**
     * @route("admin/menu/insert", name="admin_menu_insert")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertMenu(Request $request,
                               EntityManagerInterface $entityManager,
                               SluggerInterface $slugger
    )
    {
        $menu = new Menu();
        $formMenu = $this->createForm(MenuType::class, $menu);
        $formMenu->handleRequest($request);

        if ($formMenu->isSubmitted() && $formMenu->isValid()) {

            $file = $formMenu->get('file')->getData();
            $image = $formMenu->get('image')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('menus_directory'),
                    $newFilename);
                $menu->setFile($newFilename);
            }

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('menus_directory'),
                    $newFilename);
                $menu->setImage($newFilename);
            }


            $entityManager->persist($menu);
            $entityManager->flush();

            $this->addFlash('success', "Le menu a bien été créé !");

        }
        return $this->render('admin/menus/menu_insert.html.twig', [
            'formMenu' => $formMenu->createView()
        ]);

    }

    /**
     * @route("admin/menu/search", name="admin_menu_search")
     * @param MenuRepository $menuRepository
     * @param Request $request
     * @return Response
     */
    public function searchByMenu(MenuRepository $menuRepository, Request $request)
    {
        $search = $request->query->get('search');
        $menus = $menuRepository->getByWordInMenu($search);

        return $this->render('admin/menus/menu_search.html.twig', [
            'search' => $search, 'menus' => $menus
        ]);
    }

    /**
     * @route("admin/menu/update/{id}", name="admin_menu_update")
     * @param Request $request
     * @param MenuRepository $menuRepository
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */

    public function updateMenu(
        Request $request,
        MenuRepository $menuRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        $id)

    {
        $menu = $menuRepository->find($id);
        $formMenu = $this->createForm(MenuType::class, $menu);
        $formMenu->handleRequest($request);
        if ($formMenu->isSubmitted() && $formMenu->isValid()) {

            $file = $formMenu->get('file')->getData();
            $image = $formMenu->get('image')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('menus_directory'),
                    $newFilename);
                $menu->setFile($newFilename);
            }

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('menus_directory'),
                    $newFilename);
                $menu->setImage($newFilename);
            }

            $entityManager->persist($menu);
            $entityManager->flush();

            $this->addFlash('success', "Le menu a bien été modifié !");
        }

        return $this->render('admin/menus/menu_insert.html.twig', [
            'formMenu' => $formMenu->createView()
        ]);
    }

    /**
     * @route("admin/menu/delete/{id}", name="admin_menu_delete")
     * @param MenuRepository $menuRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteEvent(
        Request $request,
        MenuRepository $menuRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $menu = $menuRepository->find($id);
        $entityManager->remove($menu);
        $entityManager->flush();

        return $this->render('admin/menus/menu_delete.html.twig', [
            'menu' => $menu
        ]);
    }

}