<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PictureController extends AbstractController
{
    /**
     * //* @Route("/admin/pictures", name="admin_pictures_list")
     * @param PictureRepository $pictureRepository
     * @return Response
     */

    public function pictures(PictureRepository $pictureRepository)
    {
        $pictures = $pictureRepository->findAll();
        return $this->render('admin/pictures/pictures.html.twig', [
            'pictures' => $pictures
        ]);
    }

    /**
     * @route("admin/picture/show/{id}", name="admin_picture_show")
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
     * @route("admin/picture/insert", name="admin_picture_insert")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertPicture(Request $request,
                                  EntityManagerInterface $entityManager,
                                  SluggerInterface $slugger
    )
    {
        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        if ($formPicture->isSubmitted() && $formPicture->isValid()) {

            $image = $formPicture->get('image')->getData();

            if ($image) {

                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('gallery_directory'),
                    $newFilename);

                $picture->setImage($newFilename);
            }

            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('success', "La photo a bien été uploadée !");

        }
        return $this->render('admin/pictures/picture_insert.html.twig', [
            'formPicture' => $formPicture->createView()
        ]);

    }

    /**
     * @route("admin/picture/search", name="admin_picture_search")
     * @param PictureRepository $pictureRepository
     * @param Request $request
     * @return Response
     */
    public function searchByPicture(PictureRepository $pictureRepository, Request $request)
    {
        $search = $request->query->get('search');
        $pictures = $pictureRepository->getByWordInPicture($search);

        return $this->render('admin/pictures/picture_search.html.twig', [
            'search' => $search, 'pictures' => $pictures
        ]);
    }

    /**
     * @route("admin/picture/update/{id}", name="admin_picture_update")
     * @param Request $request
     * @param PictureRepository $pictureRepository
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updatePicture(
        Request $request,
        PictureRepository $pictureRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $picture = $pictureRepository->find($id);
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);
        if ($formPicture->isSubmitted() && $formPicture->isValid()) {

            $image = $formPicture->get('image')->getData();

            if ($image) {

                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('gallery_directory'),
                    $newFilename);

                $picture->setImage($newFilename);
            }

            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('success', "L'élément a bien été modifié !");
        }

        return $this->render('admin/pictures/picture_insert.html.twig', [
            'formPicture' => $formPicture->createView()
        ]);
    }

    /**
     * @route("admin/picture/delete/{id}", name="admin_picture_delete")
     * @param PictureRepository $pictureRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deletePicture(
        Request $request,
        PictureRepository $pictureRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $picture = $pictureRepository->find($id);
        $entityManager->remove($picture);
        $entityManager->flush();

        $this->addFlash('success', "L'élément' a bien été supprimé !");

        return $this->render('admin/pictures/picture_delete.html.twig', [
            'picture' => $picture
        ]);
    }

}