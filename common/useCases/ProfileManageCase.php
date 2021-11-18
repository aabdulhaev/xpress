<?php

declare(strict_types=1);

namespace common\useCases;

use common\forms\AddVideoPresentationCoachForm;
use common\forms\AssignSubjectForm;
use common\forms\AvatarForm;
use common\forms\ResetPasswordForm;
use common\forms\UserUpdatePasswordForm;
use common\models\User;
use common\repositories\UserRepository;
use common\services\TransactionManager;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Yii;
use yii\helpers\FileHelper;

class ProfileManageCase
{
    public $repo;
    private $transaction;

    public function __construct(UserRepository $repo, TransactionManager $transaction)
    {
        $this->repo = $repo;
        $this->transaction = $transaction;
    }

    public function editAvatar(string $user_uuid, AvatarForm $form)
    {
        $user = $this->repo->get($user_uuid);
        $user->editAvatar($form->avatar);
        $this->repo->save($user);
    }

    public function removeAvatar(string $user_uuid)
    {
        $user = $this->repo->get($user_uuid);
        $user->removeAvatar();
        $this->repo->save($user);
    }

    public function resetPassword(string $user_uuid, ResetPasswordForm $form): void
    {
        $user = $this->repo->get($user_uuid);
        $user->resetPassword($form->password);
        $this->repo->save($user);
    }

    public function setPassword(string $user_uuid, UserUpdatePasswordForm $form): void
    {
        $user = $this->repo->get($user_uuid);
        $user->updatePassword($form->password);
        $this->repo->save($user);
    }

    /**
     * @param string $user_uuid
     * @param AssignSubjectForm $form
     * @throws \Exception
     */
    public function assignSubjects(string $user_uuid, AssignSubjectForm $form): void
    {
        /** @var User $user */
        $user = $this->repo->get($user_uuid);

        $this->transaction->wrap(function () use ($user, $form) {
            $user->revokeSubjects();
            $this->repo->save($user);
            foreach ($form->subjects as $subject) {
                $user->assignSubject($subject);
            }
            $this->repo->save($user);
        });
    }

    /**
     * @param User $user
     * @param AddVideoPresentationCoachForm $form
     * @throws \Exception
     */
    public function uploadVideoPresentationCoach(User $user, AddVideoPresentationCoachForm $form): void
    {
        $fileName = $user->user_uuid . '.mp4';
        $path = Yii::getAlias('@storageRoot/origin/video-presentation/' . $user->user_uuid);
        FileHelper::createDirectory($path, 0775, true);

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout' => 3600, // The timeout for the underlying process
            'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
        ]);

        $video = $ffmpeg->open($form->video->tempName);

        $video
            ->filters()
            ->resize(new Dimension(480, 270))
            ->synchronize();

        $video->save(new X264(), $path . '/' . $fileName);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function deleteVideoPresentationCoach(User $user): void
    {
        $path = Yii::getAlias('@storageRoot/origin/video-presentation/' . $user->user_uuid . '/' . $user->user_uuid . '.mp4');

        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
