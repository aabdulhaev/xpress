<?php

namespace common\useCases;

use common\models\User;
use common\forms\LoginForm;
use common\repositories\UserRepository;

class AuthCase
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function auth(LoginForm $form): User
    {
        $user = $this->users->findByEmail($form->login);
        if (!$user || !$user->isActive() || !$user->validatePassword($form->password)) {
            throw new \DomainException('Не правильный логин или пароль.');
        }
        return $user;
    }
}
