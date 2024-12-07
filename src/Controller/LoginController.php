<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
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
     * Permet de gérer la déconnexion du compte utilisateur
     *
     * @return void
     */
    #[Route('/logout', name:'app_logout', methods: ['GET'])]
    public function logout():never
    {
    
    }
}
