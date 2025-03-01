<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Services\SendEmail;
use App\Repository\UserRepository;
use App\Form\ForgottenPasswordType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class LoginController extends AbstractController
{
    private $sendEmail;

    public function __construct(SendEmail $sendEmail) 
    {
        $this->sendEmail = $sendEmail;
    }

    /**
     * Permet de gérer la connexion au compte utilisateur
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération d'une éventuelle erreur d'authentification
        $error = $authenticationUtils->getLastAuthenticationError();
      
        // Récupération du dernier identifiant ou email saisi dans le formulaire de login
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    /**
     * Permet de gérer la récupération du mot de passe de l'utilisateur
     */
    #[Route('/forgotten_password', name:'app_forgotten_password')]
    public function forgottenPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $entityManagerInterface) 
    {
        // Création du formulaire de demande de réinitialisation du mot de passe
        $form = $this->createForm(ForgottenPasswordType::class);
        // Récupération des données du formulaire via la requête
        $form->handleRequest($request);
        // Vérification des données (soumission et validité)
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération des données saisies via le formulaire
            $data = $form->getData();
            // On vérifie si l'email saisi correspond à un utilisateur enregistré en base de données
            $user = $userRepository->findOneByEmail($data['email']);
            // Si aucun utilisateur n'existe avec cette adresse email
            if(!$user) {
                // Envoi d'un message flash
                $this->addFlash("danger", "Cette adresse email n'existe pas !");
                // Redirection vers la page de login
                return $this->redirectToRoute('app_login');
            }
            // Si un utilisateur existe, on génère un token de réinitialisation
            $resetToken = $tokenGeneratorInterface->generateToken();
            // Envoi du token de réinitialisation en base de données
            try {
                $user->setResetToken($resetToken);
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();
            } catch (\Exception $e) {
                // En cas d'échec, on envoie un message flash
                $this->addFlash("danger","Une erreur est survenue : ". $e->getMessage());
                // Redirection vers la page de login
                return $this->redirectToRoute('app_login');
            }

            // Paramétrage de l'email de demande réinitialisation du mot de passe
            $from = "contact@xavier-david.com";
            $to = $user->getEmail();
            $subject = "Réinitialisation de votre mot de passe";
            $htmlTemplate = "email/reset_password.html.twig";
            $context = [
                'user' => $user,
                'resetToken' => $resetToken
            ];
            // Envoi de l'email de réinitialisation
            try {
                $this->sendEmail->send($from, $to, $subject, $htmlTemplate, $context);
                // Envoi d'un message flash
                $this->addFlash("success", "Un email vient de vous être envoyé à l'adresse " . $user->getEmail(). " pour réinitialiser votre mot de passe.");
                // Rédirection vers la page de login
                return $this->redirectToRoute('app_login');
            } catch (TransportExceptionInterface $e) {
                // Envoi d'un message flash
                $this->addFlash("danger", "Un problème est survenu, veuillez contacter l'administrateur du site.");
                // Redirection vers la page d'accueil du site
                return $this->redirectToRoute('app_home');
            }
        }

        $formView = $form->createView();
    
        return $this->render('login/forgotten_password.html.twig', [
            'formView' => $formView
        ]);
    } 
    
    /**
     * Permet de gérer la réinitialisation du mot de passe de l'utilisateur
     */
    #[Route('/reset_password/{resetToken}', name:'app_reset_password')]
    public function resetPassword($resetToken, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        // Récupération de l'utilisateur correspondant au resetToken
        $user = $userRepository->findOneBy(['resetToken'=>$resetToken]);
        // Si aucun utilisateur n'existe avec ce token
        if(!$user) {
            // Envoi d'un message flash
            $this->addFlash("danger", "Aucun utilisateur reconnu avec ce token de réinitialisation !");
            // Redirection vers la page de login
            return $this->redirectToRoute('app_login');
        }
        // Si un utilisateur existe avec ce token, on crée le formulaire de réinitialisation du mot de passe
        $form = $this->createForm(ResetPasswordType::class, $user);
        // Récupération de la requête
        $form->handleRequest($request);
        // Vérification de la soumission et de la validité des données
        if($form->isSubmitted() && $form->isValid()) {
            // Suppression du token de réinitialisation
            $user->setResetToken(null);
            // Récupération du nouveau mot de passe saisi dans le formulaire de réinitialisation
            $new_password = $form->get('new_password')->getData();
            // Hashage du nouveau mot de passe
            $password = $userPasswordHasherInterface->hashPassword($user, $new_password);
            // Affectation du nouveau mot de passe hashé à l'entité User
            $user->setPassword($password);
            // Envoi du nouveau mot de passe en base de données
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            // Envoi d'un message flash
            $this->addFlash("success", "Mot de passe réinitialisé avec succès !");
            // Redirection vers la page de login
            return $this->redirectToRoute('app_login');
        }

        $formView = $form->createView();
        return $this->render('login/reset_password.html.twig', [
            'formView' => $formView,
            'user' => $user
        ]);
    }

    /**
     * Permet de gérer la déconnexion du compte utilisateur
     *
     * @return void
     */
    #[Route('/logout', name:'app_logout', methods: ['GET'])]
    public function logout():never
    {
    
    }
}
