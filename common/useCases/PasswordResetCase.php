<?php

namespace common\useCases;

use common\forms\PasswordResetRequestForm;
use common\forms\ResetPasswordForm;
use common\models\User;
use common\repositories\UserRepository;
use yii\mail\MailerInterface;

class PasswordResetCase
{
    private $userRepo;

    public function __construct(UserRepository $userRepo, MailerInterface $mailer)
    {
        $this->userRepo = $userRepo;
    }

    public function request(PasswordResetRequestForm $form): void
    {
        $user = $this->userRepo->getByEmail($form->email);

        if ($user->isArchive()) {
            throw new \DomainException('Пользователь заблокирован.');
        }

        $user->requestPasswordReset();
        $this->userRepo->save($user);
    }

    public function validateToken($token): void
    {
        if (empty($token) || !is_string($token)) {
            throw new \DomainException('Токен сброса пароля не может быть пустым');
        }
        if (!$this->userRepo->existsByPasswordResetToken($token)) {
            throw new \DomainException("Токен {$token} сброса пароля не найден");
        }
    }

    public function reset(string $token, ResetPasswordForm $form): void
    {
        $user = $this->userRepo->getByPasswordResetToken($token);
        $user->resetPassword($form->password);
        if ($user->isWait()){
            $user->status = User::STATUS_ACTIVE;
        }
        $this->userRepo->save($user);
    }
}
