<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\SmartObject;

final class CommentFacade
{
    use SmartObject;

    public function __construct(
        private Explorer $database
    ) {}

    /**
     * Přidá komentář k receptu
     */
    public function add(int $recipeId, int $authorId, string $content): void
    {
        $this->database->table('comment')->insert([
            'recipe_id' => $recipeId,
            'author_id' => $authorId,
            'content' => $content,
        ]);
    }

    /**
     * Vrátí všechny komentáře k danému receptu
     */
    public function getByRecipe(int $recipeId)
    {
        return $this->database
            ->table('comment')
            ->where('recipe_id', $recipeId)
            ->order('created_at DESC');
    }
}
