<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Security\Passwords;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\SimpleIdentity;
use Nette\Security\User;

final class AuthFacade 
{


    public function __construct(
        private Explorer $database,
        private Passwords $passwords,
        private User $user,
    ) {}

    public function createNewUser(string $name, string $email, string $password): void
    {
        try {
            $this->database->table('user')->insert([
                'name' => $name,
                'email' => $email,
                'password' => $this->passwords->hash($password),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new UniqueConstraintViolationException('Email already exists');
        }
    }

    public function login(string $email, string $password): bool
    {
        $row = $this->database->table('user')->where('email', $email)->fetch();
        if (!$row || !$this->passwords->verify($password, $row->password)) {
            return false;
        }
    
        $identity = new SimpleIdentity(
            $row->id,
            [$row->role], 
            [
                'id' => $row->id,
                'email' => $row->email,
                'name' => $row->name,
            ]
        );
    
        $this->user->login($identity);
        return true;
    }
    



}