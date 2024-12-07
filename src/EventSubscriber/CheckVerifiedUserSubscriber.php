<?php  

namespace App\EventSubscriber;

use App\Entity\User as AppUser;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

/**
 * Ecouteur d'évènements permettant de gérer la réponse à l'issue de la procédure d'authentification
 */
class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
  private $routerInterface;

  /**
   * RouterInterface permettant de générer une URL
   *
   * @param RouterInterface $routerInterface
   */
  public function __construct(RouterInterface $routerInterface)
  {
    $this->routerInterface = $routerInterface;
  }

  /**
   * Récupération des évènements liés à l'authentification
   *
   * @return void
   */
  public static function getSubscribedEvents() 
  {
    
    // Retourne les évènements écoutés
    return [
      // Evènement CheckPassportEvent du composant Security écoutant la procédure d'authentification en cours 
      CheckPassportEvent::class => ['onCheckPassport', -10],
      // Evènement LoginFailureEvent du composant Security écoutant un éventuel echec d'authentification
      LoginFailureEvent::class => ['onLoginFailure']
    ];
  }

  /**
   * Ecoute de l'évènement CheckPassportEvent pour interrompre la procédure d'authentification en cours, vérifier l'activation du compte et valider ou non l'authentification de l'utilisateur
   *
   * @param CheckPassportEvent $checkPassportEvent
   * @return void
   */
  public function onCheckPassport(CheckPassportEvent $checkPassportEvent) 
  {
    // Récupération du passeport d'autehntification
    $passport = $checkPassportEvent->getPassport();
    // Récupération de l'utilisateur en cours d'authentification
    $user = $passport->getUser();
    if (!$user instanceof AppUser) {
      return;
    }
    // Si le compte de l'utilisateur n'est pas activé (si le token d'activation n'est pas null)
    if ($user->getActivationToken() !== null) {
        // On lance une exception avec un message d'erreur
        throw new AccountNotVerifiedAuthenticationException();
    }
  }

  /**
   * Ecoute de l'évènement LoginFailureEvent pour statuer sur l'échec de l'authentification (identificants invalides) et remplacer le comportement d'échec par défaut (redirection personnalisée)
   *
   * @param LoginFailureEvent $loginFailureEvent
   * @return void
   */
  public function onLoginFailure(LoginFailureEvent $loginFailureEvent) 
  {
    // Récupération du passeport d'authentification
    $passport = $loginFailureEvent->getPassport();
    // Récupération de l'utilisateur en cours d'authentification
    $userId = $passport->getUser();
    
    if (!$loginFailureEvent->getException() instanceof AccountNotVerifiedAuthenticationException) {
      return;  
    }
    // Gestion de la réponse et de la redirection vers une page personnalisée en cas d'échec d'authentification
    $response = new RedirectResponse($this->routerInterface->generate('app_activation_alert', array('id' => $userId)));
    $loginFailureEvent->setResponse($response);
  }
}
 





