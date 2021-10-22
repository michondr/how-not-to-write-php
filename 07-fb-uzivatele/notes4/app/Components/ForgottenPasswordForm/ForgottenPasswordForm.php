<?php

namespace App\Components\ForgottenPasswordForm;

use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class ForgottenPasswordForm
 * @package App\Components\ForgottenPasswordForm
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

  /**
   * ForgottenPasswordForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param UsersFacade $usersFacade
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, UsersFacade $usersFacade){
    parent::__construct($parent, $name);
    $this->usersFacade=$usersFacade;
    $this->createSubcomponents();
  }

  private function createSubcomponents(){
    $this->addEmail('email','E-mail')
      ->setRequired('Zadejte platný email');

    $this->addSubmit('ok','poslat e-mail pro obnovu hesla')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){

        try{
            $user = $this->usersFacade->getUserByEmail($this->values->email);
        } catch (\Exception $e){
            $this->onFinished('Pokud je email registrovaný, poslali jsme link na reset hesla (nemame)');;
            return;
        }

        $forgottenPassword = $this->usersFacade->saveNewForgottenPasswordCode($user);
        $mailLink = $this->getPresenter()->link('//User:renewPassword', ['user' => $user->userId, 'code' => $forgottenPassword->code]);


        $mail = new Nette\Mail\Message();
        $mail->setFrom('mico00@vse.cz', 'Tvoje máma');
        $mail->addTo($this->values->email);
        $mail->setSubject('Reset password');
        $mail->setHtmlBody('go to <a href="'.$mailLink.'">here</a> to reset your password ');

        $mailer = new Nette\Mail\SmtpMailer();
        $mailer->send($mail);

        $this->onFinished('Pokud je email registrovaný, poslali jsme link na reset hesla (mame)');;


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