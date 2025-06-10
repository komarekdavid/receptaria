<?php

namespace App\Presentation\User;

use App\Model\UserFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

final class UserPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private UserFacade $userFacade,
        private User $user,
    ) {}

    public function startup(): void
    {
        parent::startup();

        if (!$this->user->isInRole('admin')) {
            $this->flashMessage('Přístup odepřen.', 'error');
            $this->redirect('Homepage:');
        }
    }

    public function renderDefault(): void
    {
        $this->template->users = $this->userFacade->getAllUsers();
    }

    public function actionEdit(int $id): void
    {
        $user = $this->userFacade->getUserById($id);
        if (!$user) {
            $this->error('Uživatel nebyl nalezen.');
        }

        $this['editForm']->setDefaults($user->toArray());
    }

    protected function createComponentEditForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Jméno:')
            ->setRequired();

        $form->addEmail('email', 'E-mail:')
            ->setRequired();

        $form->addPassword('password', 'Nové heslo:')
            ->setOption('description', 'Nechej prázdné, pokud nechceš měnit heslo.');

        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    public function editFormSucceeded(Form $form, array $values): void
    {
        $id = $this->getParameter('id');

        $data = [
            'name' => $values['name'],
            'email' => $values['email'],
        ];

        if ($values['password'] !== '') {
            $data['password'] = $values['password'];
        }

        $this->userFacade->updateUser((int)$id, $data);

        $this->flashMessage('Uživatel byl upraven.', 'success');
        $this->redirect('this');
    }

    public function actionDelete(int $id): void
    {
        $identity = $this->getUser()->getIdentity();

        if (!$identity) {
            $this->flashMessage('Nejste přihlášený.', 'error');
            $this->redirect('Sign:in');
            return;
        }

        if ((int) $identity->id === $id) {
            $this->flashMessage('Nemůžete smazat sami sebe.', 'error');
            $this->redirect('User:');

            return;
        }

        $this->userFacade->deleteUser($id);
        $this->flashMessage('Uživatel byl smazán.', 'success');
        $this->redirect('User:');
    }

    
}
