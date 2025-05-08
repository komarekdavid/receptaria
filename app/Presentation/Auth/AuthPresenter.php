<?php


namespace App\Presentation\Auth;
use App\Model\AuthFacade;
use Nette;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;

final class AuthPresenter extends Nette\Application\UI\Presenter
{
    private AuthFacade $authFacade;
    public function __construct(AuthFacade $authFacade)
    {
        parent::__construct();
        $this->authFacade = $authFacade;
    }

    protected function createComponentRegisterForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('name', 'Jméno:')
            ->setRequired('Prosím zadejte své jméno.');
        $form->addEmail('email', 'Email:')
            ->setRequired('Prosím zadejte svůj email.')
            ->addRule($form::EMAIL, 'Prosím zadejte platný email.');
        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím zadejte heslo.')
            ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň 8 znaků', 8);
        $form->addSubmit('register', 'Registrovat');
        $form->onSuccess[] = [$this, 'registerFormSucceeded'];
        return $form;
    }

    public function registerFormSucceeded(Form $form, \stdClass $data): void
    {
        try {
            $this->authFacade->createNewUser($data->name, $data->email, $data->password);
            $this->flashMessage('Registrace úspěšná!', 'success');
            $this->redirect('Home:default');
        } catch (Nette\Database\UniqueConstraintViolationException $e) {
            $form['email']->addError('Tento email je již registrován');
        }
    }


    protected function createComponentLoginForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();
        $form->addEmail('email', 'Email:')
            ->setRequired('Prosím zadejte svůj email.')
            ->addRule($form::EMAIL, 'Prosím zadejte platný email.');
        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím zadejte heslo.');
        $form->addSubmit('login', 'Přihlásit se');
        $form->onSuccess[] = [$this, 'loginFormSucceeded'];
        return $form;
    }

    public function loginFormSucceeded(Form $form, \stdClass $data): void
    {
        if ($this->authFacade->login($data->email, $data->password)) {
            $this->flashMessage('Přihlášení úspěšné!', 'success');
            $this->redirect('Home:default');
        } else {
            $form['password']->addError('Neplatné přihlašovací údaje.');
        }
    }


    public function actionLogout(): void
    {
        $this->getUser()->logout(true); 
        $this->flashMessage('Byl jste úspěšně odhlášen.', 'info');
        $this->redirect('Home:default');
    }



    

}