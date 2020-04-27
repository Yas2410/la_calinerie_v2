<?php

namespace App\Controller\Front;

use App\Entity\Article;
use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Repository\EventRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeParentsController extends AbstractController
{
    /**
     * @Route("/parents", name="home_parents")
     * @param ArticleRepository $articleRepository
     * @param EventRepository $eventRepository
     * @param PictureRepository $pictureRepository
     * @return Response
     */
    public function homeParents(
        ArticleRepository $articleRepository,
        EventRepository $eventRepository,
        PictureRepository $pictureRepository
    )
    {
        $lastArticles = $articleRepository->findBy([], ['id' => 'DESC'], 3, 0);
        $lastEvents = $eventRepository->findBy([], ['id' => 'DESC'], 3, 0);
        $lastPictures = $pictureRepository->findBy([], ['id' => 'DESC'], 3, 0);
        return $this->render('front/home/homeParents.html.twig', [
            'articles' => $lastArticles,
            'events' => $lastEvents,
            'pictures' => $lastPictures
        ]);
    }

}


