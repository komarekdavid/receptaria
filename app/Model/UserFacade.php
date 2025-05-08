<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Security\Passwords;

final class UserFacade
{
    public function __construct(
        private Explorer $db,
        private Passwords $passwords,
    ) {}

    public function getAllUsers(): \Nette\Database\Table\Selection
    {
        return $this->db->table('user');
    }

    public function getUserById(int $id): ?\Nette\Database\Table\ActiveRow
    {
        return $this->db->table('user')->get($id);
    }

    public function updateUser(int $id, array $data): void
    {
        if (isset($data['password'])) {
            $data['password'] = $this->passwords->hash($data['password']);
        }

        $this->db->table('user')->get($id)?->update($data);
    }

    public function deleteUser(int $id): void
    {
        $this->db->table('user')->get($id)?->delete();
    }
}
