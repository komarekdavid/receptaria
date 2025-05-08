<?php

declare(strict_types=1);

namespace App\Presentation\Home;

use Nette;
use Nette\Database\Explorer;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function renderDefault(): void
    {
        $this->template->topUsers = $this->database->query('
            SELECT user.*, COUNT(recipe.id) AS recipe_count
            FROM user
            LEFT JOIN recipe ON recipe.author_id = user.id
            GROUP BY user.id
            ORDER BY recipe_count DESC
            LIMIT 3
        ')->fetchAll();

        $this->template->topRecipes = $this->database
            ->table('recipe')
            ->order('views_count DESC')
            ->limit(3);
    }
}
