<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\ChildRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Swift_Mailer $mailer
     * @param UserRepository $userRepository
     * @param ChildRepository $childRepository
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                             Swift_Mailer $mailer, UserRepository $userRepository,
                             ChildRepository $childRepository): Response
    {
        /* Je créé ici une nouvelle instance de User et la lie à mon formulaire */
        $user = new User();

        /* Je créé mon formulaire (RegistrationFormType) qui va permettre à l'utilisateur
        de s'enregistrer et permettre la création du compte donnant accès à l'espace Parents */
        $form = $this->createForm(RegistrationFormType::class, $user);

        /* La méthode "handleRequest() va permettre de gérer les données de la requête POST */
        $form->handleRequest($request);

        /* Si le formulaire envoyé est valide : */
        if ($form->isSubmitted() && $form->isValid()) {

            /* 1. Je procède à l'encodage du mot de passe */
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            /* 2. Pour chaque inscription, un token va être généré et enregistré.
            Ce dernier va permettre à l'utilisateur d'activer son compte via le lien
            d'activation reçu par mail (renseigné à l'inscription) */
            $user->setActivationToken(md5(uniqid()));
            /* Je 'persist' et 'flush' mon instance $user afin d'enregistrer
            les données en base de données */
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            /* Mail d'activation du compte */
            $message = (new \Swift_Message('Nouveau compte'))
                // On attribue l'expéditeur
                ->setFrom(['crechelacalinerie@gmail.com' => 'LA CALINERIE '])
                // On attribue le destinataire
                ->setTo($user->getEmail())
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig', ['token' => $user->getActivationToken()]
                    ),
                    'text/html'
                );
            /* On procède à l'envoi du mail */
            $mailer->send($message);
            /* La vue sur laquelle l'utilisateur est redirigé une fois le formulaire
            d'inscription validé */
            return $this->render('emails/confirmation.html.twig');
        }

        /* Création de ma vue et renvoi du formulaire créé dans ce même fichier twig */
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     * @param $token
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function activation($token, UserRepository $userRepository)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $userRepository->findOneBy(['activationToken' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // S'il y a association :
        // on supprime le token (qui va passer en null en BDD)
        $user->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        // j'attribue à l'utilisateur le role qui va lui permettre
        // d'avoir accès à l'espace Parents du site
        $user->setRoles(["ROLE_PARENT"]);
        //J'enregistre les données en BDD
        $entityManager->persist($user);
        $entityManager->flush();

        // Je renvoie l'utilisateur sur la page publique du site
        // où se dernier pourra désormais se connecter à l'espace Parents
        return $this->redirectToRoute('home');
    }
}
