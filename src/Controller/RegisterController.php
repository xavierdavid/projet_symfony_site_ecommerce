<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Services\SendEmail;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterController extends AbstractController
{
    private $sendEmail;

    public function __construct(SendEmail $sendEmail) 
    {
        $this->sendEmail = $sendEmail;
    }

    /**
     * Affichage et le traitement du formulaire d'inscription
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManagerInterface
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher, TokenGeneratorInterface $tokenGeneratorInterface): Response
    {
        // Création d'un nouvel objet utilisateur
        $user = new User;
        // Construction du formulaire d'inscription lié à l'entité User
        $form = $this->createForm(RegisterType::class, $user);
        // Traitement de la requête
        $form->handlerequest($request);
        // Vérification des données
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération du mot de passe en clair
            $plainPassword = $user->getPassword();
            // Hachage du mot de passe de l'utilisateur
            $hashPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashPassword);
            // Création du token d'activation
            $activationToken = $tokenGeneratorInterface->generateToken();
            $user->setActivationToken($activationToken);
             // Préparation de l'envoi à l'aide du manager de doctrine
             $entityManagerInterface->persist($user);
             // Envoi en base de données
             $entityManagerInterface->flush();
            // Paramétrage de l'email d'activation de compte
            $from = "contact@xavier-david.com";
            $to = $user->getEmail();
            $subject = "Activation de votre compte";
            $htmlTemplate = "email/activation.html.twig";
            $context = [
                'user' => $user,
                'activationToken'=> $activationToken
            ];
            // Envoi de l'email d'activation de compte
            try {
                $this->sendEmail->send($from, $to, $subject, $htmlTemplate, $context);
                // Redirection vers la page d'activation en cours
                return $this->redirectToRoute('app_activation_in_progress', array('id' => $user->getId()));
            } catch (TransportExceptionInterface $e) {
                // Message d'erreur
                $this->addFlash('warning', 'Un problème est survenu, veuillez contacter l\'administrateur du site');
                // Redirection vers la page d'accueil
                return $this->redirectToRoute('app_homepage');
            }
        }
        $formView = $form->createView();
        return $this->render('register/index.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * Activation en cours du compte de l'utilisateur
     *
     * @param [type] $id
     * @param UserRepository $userRepository
     * @return void
     */
    #[Route('/activation_in_progress/{id}', name: 'app_activation_in_progress')]
    public function activationInProgress($id, UserRepository $userRepository)
    {   
        // Récupération de l'utilisateur en cours d'authentification
        $user = $userRepository->find($id);
        return $this->render('register/activation_in_progress.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Alerte de compte utilisateur non activé
     *
     * @param [type] $id
     * @param UserRepository $userRepository
     * @return void
     */
    #[Route('/activation_alert/{id}', name: 'app_activation_alert')]
    public function activationAlert($id, UserRepository $userRepository)
    {   
        // Récupération de l'utilisateur en cours d'authentification
        $user = $userRepository->find($id);
        return $this->render('register/activation_alert.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Envoi d'un nouvel email d'activation 
     *
     * @param [type] $id
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManagerInterface
     * @return Response
     */
    #[Route('/new_activation_email/{id}}', name: 'app_register_new_activation_email')]
    public function newActivationEmail($id, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, TokenGeneratorInterface $tokenGeneratorInterface): Response
    {
        // Récupération de l'utilisateur en cours d'authentification
        $user = $userRepository->find($id);
        
        // Vérification de l'inscription de l'utilisateur
        if(!$user) {
            $this->addFlash('warning', 'Vous devez préalablement vous inscrire sur le site pour accéder à cette page');
            return $this->redirectToRoute(('app_register'));
        }
        // Vérification de la présence d'un token d'activation
        if($user->getActivationToken() == null) {
            $this->addFlash('warning', "Votre compte est déjà activé");
            return $this->redirectToRoute('app_login');
        }

        // Création d'un nouveau token d'activation
        $activationToken = $tokenGeneratorInterface->generateToken();
        $user->setActivationToken($activationToken);
        // Préparation de l'envoi à l'aide du manager de doctrine
        $entityManagerInterface->persist($user);
        // Envoi en base de données
        $entityManagerInterface->flush();
        // Paramétrage de l'email d'activation de compte
        $from = "contact@xavier-david.com";
        $to = $user->getEmail();
        $subject = "Activation de votre compte";
        $htmlTemplate = "email/activation.html.twig";
        $context = [
            'user' => $user,
            'activationToken'=> $activationToken
        ];
    
        // Envoi de l'email d'activation de compte
        try {
            $this->sendEmail->send($from, $to, $subject, $htmlTemplate, $context);
            // Envoi d'un message flash indiquant qu'un lien d'activation a été envoyé par email
            $this->addFlash('success', 'Un nouvel email vient de vous être envoyé à l\'adresse ' . $user->getEmail() . ' pour activer votre compte !');
            // Redirection vers la page d'activation en cours
            return $this->redirectToRoute('app_activation_in_progress', array('id' => $user->getId()));
        } catch (TransportExceptionInterface $e) {
            // Message d'erreur
            $this->addFlash('warning', 'Un problème est survenu, veuillez contacter l\'administrateur du site');
            // Redirection vers la page d'accueil
            return $this->redirectToRoute('app_homepage');
        }
    }

    /**
     * Activation du compte utilisateur
     *
     * @param [type] $activationToken
     * @param UserRepository $repository
     * @param EntityManagerInterface $entityManagerInterface
     * @return void
     */
    #[Route('/activation/{activationToken}', name: 'app_register_activation')]
    public function activation($activationToken, UserRepository $repository, EntityManagerInterface $entityManagerInterface)
    {
        // On récupère l'utilisateur ayant le token d'activation passé en paramètre (et dont le compte n'est pas encore activé)
        $user = $repository->findOneBy(['activationToken'=> $activationToken]);
    
        // Si aucun utilisateur n'existe avec ce token
        if(!$user) {
            // On affiche une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
    
        // On supprime ensuite le token de l'utilisateur pour activer son compte
        $user->setActivationToken(null);
        // On envoie la modification en base de données à l'aide du manager de Doctrine
        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush($user);
    
        // On envoie un message flash à l'utilisateur indiquant que son compte est activé
        $this->addFlash('success', 'Votre compte est activé ! Vous pouvez vous connecter.');
    
        // On redirige vers la page de login
        return $this->redirectToRoute('app_login');
    }
}
