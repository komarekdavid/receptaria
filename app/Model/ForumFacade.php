<?php
namespace App\Model;

use Nette\Database\Explorer;
use Nette\Utils\DateTime;

final class ForumFacade
{
    public function __construct(
        private Explorer $database,
    ) {}

    public function getAll(): \Nette\Database\Table\Selection
    {
        return $this->database->table('forum_post')
            ->order('created_at DESC');
    }

    public function getById(int $id): ?\Nette\Database\Table\ActiveRow
    {
        return $this->database->table('forum_post')
            ->get($id);
    }

    public function add(array $data): void
    {
        $data['created_at'] = new DateTime();
        $this->database->table('forum_post')->insert($data);
    }

    public function update(int $id, array $data): void
    {
        $data['updated_at'] = new DateTime();
        $this->database->table('forum_post')
            ->where('id', $id)
            ->update($data);
    }

    public function delete(int $id): void
    {
        $this->database->table('forum_post')
            ->where('id', $id)
            ->delete();
    }
}
