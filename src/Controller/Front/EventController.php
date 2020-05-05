<?php

namespace App\Controller\Front;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EventController extends AbstractController
{
    /**
     * //* @Route("/parents/events", name="events")
     * @param EventRepository $eventRepository
     * @return Response
     */

    public function events(EventRepository $eventRepository)
    {
        $events = $eventRepository->findAll();
        return $this->render('front/events/events.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @route("parents/event/show/{id}", name="event")
     * @param EventRepository $eventRepository
     * @param $id
     * @return Response
     */
    public function event(EventRepository $eventRepository, $id)
    {
        $event = $eventRepository->find($id);

        return $this->render('front/events/event.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @route("parents/event/search", name="event_search")
     * @param EventRepository $eventRepository
     * @param Request $request
     * @return Response
     */
    public function searchByEvent(EventRepository $eventRepository, Request $request)
    {
        $search = $request->query->get('search');
        $events = $eventRepository->getByWordInEvent($search);

        return $this->render('front/events/event_search.html.twig', [
            'search' => $search, 'events' => $events
        ]);
    }
}