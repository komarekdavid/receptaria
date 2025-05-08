<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Utils\DateTime;

final class CategoryFacade
{
    public function __construct(
        private Explorer $database,
    ) {}

    public function getAll(): \Nette\Database\Table\Selection
    {
        return $this->database->table('category')
            ->order('created_at DESC');
    }

    public function getById(int $id): ?\Nette\Database\Table\ActiveRow
    {
        return $this->database->table('category')->get($id);
    }

    public function add(string $name, ?string $description = null): void
    {
        $this->database->table('category')->insert([
            'name' => $name,
            'description' => $description,
            'created_at' => new DateTime(),
        ]);
    }

    public function delete(int $id): void
    {
        $this->database->table('category')->where('id', $id)->delete();
    }


    public function update(int $id, string $name, ?string $description = null): void
    {
        $this->database->table('category')->where('id', $id)->update([
            'name' => $name,
            'description' => $description,
        ]);
    }


}
