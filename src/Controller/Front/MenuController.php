<?php

namespace App\Controller\Front;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * //* @Route("/parents/menus", name="menus")
     * @param MenuRepository $menuRepository
     * @return Response
     */

    public function menus(MenuRepository $menuRepository)
    {
        $menus = $menuRepository->findAll();
        return $this->render('front/menus/menus.html.twig', [
            'menus' => $menus
        ]);
    }

    /**
     * @route("front/menu/show/{id}", name="menu")
     * @param MenuRepository $menuRepository
     * @param $id
     * @return Response
     */
    public function menu(MenuRepository $menuRepository, $id)
    {
        $menu = $menuRepository->find($id);

        return $this->render('front/menus/menu.html.twig', [
            'menu' => $menu
        ]);
    }

    /**
     * @route("front/menu/search", name="menu_search")
     * @param MenuRepository $menuRepository
     * @param Request $request
     * @return Response
     */
    public function searchByMenu(MenuRepository $menuRepository, Request $request)
    {
        $search = $request->query->get('search');
        $menus = $menuRepository->getByWordInMenu($search);

        return $this->render('front/menus/menu_search.html.twig', [
            'search' => $search, 'menus' => $menus
        ]);
    }
}