<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;

final class HomeFacade
{
    private Explorer $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function getTopUsers(): array
    {
        return $this->database->query('
            SELECT user.*, COUNT(recipe.id) AS recipe_count
            FROM user
            LEFT JOIN recipe ON recipe.author_id = user.id
            GROUP BY user.id
            ORDER BY recipe_count DESC
            LIMIT 3
        ')->fetchAll();
    }

    public function getTopRecipes()
    {
        return $this->database
            ->table('recipe')
            ->order('views_count DESC')
            ->limit(3);
    }
}
