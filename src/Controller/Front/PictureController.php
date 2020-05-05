<?php

namespace App\Controller\Front;

use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    /**
     * //* @Route("/parents/pictures", name="pictures")
     * @param PictureRepository $pictureRepository
     * @return Response
     */

    public function pictures(PictureRepository $pictureRepository)
    {
        $pictures = $pictureRepository->findAll();
        return $this->render('front/pictures/pictures.html.twig', [
            'pictures' => $pictures
        ]);
    }

    /**
     * @route("parents/picture/show/{id}", name="picture")
     * @param PictureRepository $pictureRepository
     * @param $id
     * @return Response
     */
    public function picture(PictureRepository $pictureRepository, $id)
    {
        $picture = $pictureRepository->find($id);

        return $this->render('admin/pictures/picture.html.twig', [
            'picture' => $picture
        ]);
    }

    /**
     * @route("parents/picture/search", name="picture_search")
     * @param PictureRepository $pictureRepository
     * @param Request $request
     * @return Response
     */
    public function searchByPicture(PictureRepository $pictureRepository, Request $request)
    {
        $search = $request->query->get('search');
        $pictures = $pictureRepository->getByWordInPicture($search);

        return $this->render('front/pictures/picture_search.html.twig', [
            'search' => $search, 'pictures' => $pictures
        ]);
    }
}