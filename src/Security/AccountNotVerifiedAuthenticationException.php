<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Classe d'exception personnalisée indiquant l'échec d'authentification en raison de la non-activation du compte utilisateur
 */
class AccountNotVerifiedAuthenticationException extends AuthenticationException
{

}
