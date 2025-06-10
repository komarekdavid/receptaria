<?php

declare(strict_types=1);

namespace App\Presentation\Home;

use Nette\Application\UI\Presenter;
use App\Model\HomeFacade;

final class HomePresenter extends Presenter
{
    private HomeFacade $homeFacade;

    public function __construct(HomeFacade $homeFacade)
    {
        parent::__construct(); 
        $this->homeFacade = $homeFacade;
    }

    public function renderDefault(): void
    {
        $this->template->topUsers = $this->homeFacade->getTopUsers();
        $this->template->topRecipes = $this->homeFacade->getTopRecipes();
    }
}
