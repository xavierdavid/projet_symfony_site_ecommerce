<?php  

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendEmail {

  private $mailerInterface;

  public function __construct(MailerInterface $mailerInterface) 
  {
    $this->mailerInterface = $mailerInterface;
  }

  /**
   * Permet d'envoyer des emails
   *
   * @param string $from
   * @param string $to
   * @param string $subject
   * @param string $htmlTemplate
   * @param array $context
   * @return void
   */
  public function send(string $from, string $to, string $subject, string $htmlTemplate, array $context): void 
  {
    // Création de l'email à envoyer
    $email = (new TemplatedEmail())
      // Paramètres de l'email
      ->from($from)
      ->to($to)
      ->subject($subject)
      // Chemin vers le template Twig du mail
      ->htmlTemplate($htmlTemplate)
      // Tableau de variables à passer dans le template
      ->context($context);
    
      // Envoi de l'email
      $this->mailerInterface->send($email);
  }
}