<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MentionsController extends AbstractController
{

    /**
     * @Route("/mentions_legales", name="mentions")
     */


    public function mentions()
    {

        return $this->render('front/common/mentions.html.twig');
    }
}