<?php

namespace App\Presentation\Profile;

use App\Model\RecipeFacade;
use Nette\Application\UI\Presenter;

final class ProfilePresenter extends Presenter
{
    public function __construct(
        private RecipeFacade $recipeFacade,
    ) {}

    public function renderDefault(): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Pro přístup do profilu se musíš přihlásit.', 'warning');
            $this->redirect('Auth:login');
        }

        $this->template->posts = $this->recipeFacade->getAll()
            ->where('author_id', $this->getUser()->getId());
    }
}
