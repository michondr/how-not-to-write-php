<?php

namespace App\FrontModule\Components\ForgottenPasswordForm;

use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class ForgottenPasswordForm
 * @package App\FrontModule\Components\ForgottenPasswordForm
 *
 * @method onFinished($message='')
 * @method onCancel()
 */
class ForgottenPasswordForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];

  /** @var UsersFacade $usersFacade */
  private $usersFacade;
  /** @var Nette\Application\LinkGenerator $linkGenerator */
  private $linkGenerator;
  /** @var string $mailFromEmail */
  private $mailFromEmail = '';
  /** @var string $mailFromName */
  private $mailFromName = '';

  /**
   * ForgottenPasswordForm constructor.
   * @param string $mailFromEmail
   * @param string $mailFromName
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param UsersFacade $usersFacade
   * @param Nette\Application\LinkGenerator $linkGenerator
   */
  public function __construct(string $mailFromEmail, string $mailFromName, Nette\ComponentModel\IContainer $parent = null, string $name = null, UsersFacade $usersFacade, Nette\Application\LinkGenerator $linkGenerator){
    parent::__construct($parent, $name);
    $this->usersFacade=$usersFacade;
    $this->createSubcomponents();
    $this->linkGenerator=$linkGenerator;
    $this->mailFromEmail=$mailFromEmail;
    $this->mailFromName=$mailFromName;
  }

  private function createSubcomponents(){
    $this->addEmail('email','E-mail')
      ->setRequired('Zadejte platný email');

    $this->addSubmit('ok','poslat e-mail pro obnovu hesla')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){
        //získáme z formuláře zadaný e-mail
        $userEmail = $this->values->email;

        //najdeme daného uživatele
        try{
          $user = $this->usersFacade->getUserByEmail($userEmail);
        }catch (\Exception $e){
          //uživatel nebyl nalezen - zvažte, zda o tom informovat uživatele, či nikoliv
          $this->onFinished('Pokud uživatelský účet s daným e-mailem existuje, poslali jsme vám odkaz na změnu hesla.');
          return;
        }

        //vygenerování odkaz na změnu hesla
        $forgottenPassword = $this->usersFacade->saveNewForgottenPasswordCode($user);
        $mailLink = $this->linkGenerator->link('//Front:User:renewPassword', ['user'=>$user->userId, 'code'=>$forgottenPassword->code]);

        #region příprava textu mailu
        $mail = new Nette\Mail\Message();
        $mail->setFrom($this->mailFromEmail, $this->mailFromName);
        $mail->addTo($user->email, $user->name);
        $mail->subject = 'Obnova zapomenutého hesla';
        $mail->htmlBody = 'Obdrželi jsme vaši žádost na obnovu zapomenutého hesla. Pokud si přejete heslo změnit, <a href="'.$mailLink.'">klikněte zde</a>.';
        #endregion endregion příprava textu mailu

        //odeslání mailu pomocí PHP funkce mail
        $mailer = new Nette\Mail\SendmailMailer;
        $mailer->send($mail);

        $this->onFinished();
      };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([])
      ->setHtmlAttribute('class','btn btn-light')
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

}