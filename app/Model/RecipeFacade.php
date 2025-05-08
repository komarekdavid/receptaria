<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;
use Nette\Database\SqlLiteral;


final class RecipeFacade
{
    public function __construct(
        private Explorer $database,
    ) {}

    public function getAll(): \Nette\Database\Table\Selection
    {
        return $this->database->table('recipe')
            ->order('created_at DESC');
    }

    public function getById(int $id): ?ActiveRow
    {
        return $this->database->table('recipe')
            ->get($id);
    }


    public function addRating(int $id, int $value): void
    {
        $this->database->table('recipe')
            ->where('id', $id)
            ->update([
                'rating_sum' => new SqlLiteral('rating_sum + ?', [$value]),
                'rating_count' => new SqlLiteral('rating_count + ?', [1]),
            ]);
    }
    
    

    public function getByCategory(int $categoryId): \Nette\Database\Table\Selection
    {
        return $this->database->table('recipe')
            ->where('category_id', $categoryId)
            ->order('created_at DESC');
    }

    public function add(array $data): ActiveRow
    {
        return $this->database->table('recipe')->insert([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $data['image'] ?? null,
            'author_id' => $data['author_id'],
            'category_id' => $data['category_id'],
            'duration' => $data['duration'],
            'difficulty' => $data['difficulty'],
            'ingredients' => json_encode($data['ingredients']),
            'comments_enabled' => $data['comments_enabled'] ?? true,
            'created_at' => new DateTime(),
        ]);
    }

    public function update(int $id, array $data): void
    {
        $this->database->table('recipe')->where('id', $id)->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'category_id' => $data['category_id'],
            'duration' => $data['duration'],
            'difficulty' => $data['difficulty'],
            'ingredients' => json_encode($data['ingredients']),
            'comments_enabled' => $data['comments_enabled'],
            'updated_at' => new DateTime(),
        ]);
    }

    public function delete(int $id): void
    {
        $this->database->table('recipe')->where('id', $id)->delete();
    }

    public function incrementViews(int $id): void
    {
        $this->database->table('recipe')->where('id', $id)->update([
            'views_count+=' => 1,
        ]);
    }


    public function getAverageRating(int $id): ?float
    {
        $recipe = $this->getById($id);
        if ($recipe && $recipe->rating_count > 0) {
            return $recipe->rating_sum / $recipe->rating_count;
        }
        return null;
    }
}
