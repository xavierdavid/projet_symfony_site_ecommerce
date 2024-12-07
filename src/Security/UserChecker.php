<?php
 
namespace App\Security;
 
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
 
/**
 * Vérification de l'activation du compte utilisateur
 */
class UserChecker implements UserCheckerInterface
{
   /**
    * Vérifications avant authentification de l'utilisateur
    *
    * @param UserInterface $user
    * @return void
    */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
           return;
        }
    }
 
    /**
     * Vérifications après authentification de l'utilisateur
     *
     * @param UserInterface $user
     * @return void
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
           return;
        }
        // Si le compte de l'utilisateur n'est pas activé (si le token d'activation n'est pas null)
        if ($user->getActivationToken() !== null) {
           // On lance une exception avec un message d'erreur
           throw new CustomUserMessageAccountStatusException("Votre compte utilisateur n\'est pas encore activé. Veuillez confirmer votre inscription en cliquant sur le lien d'activation qui vous a été envoyé par email ! Pensez à vérifier dans vos spams");
        }
    }
}
