<?php

namespace common\useCases;

use common\access\Rbac;
use common\forms\UserCreateForm;
use common\models\Program;
use common\models\User;
use common\repositories\UserRepository;
use common\services\RoleManager;
use common\services\TransactionManager;
use DomainException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class SignupCase
{
    private $users;
    private $roles;
    private $transaction;
    private $manageCase;

    public function __construct(
        UserRepository $users,
        RoleManager $roles,
        TransactionManager $transaction,
        UserManageCase $manageCase
    ) {
        $this->users = $users;
        $this->roles = $roles;
        $this->transaction = $transaction;
        $this->manageCase = $manageCase;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function signup(UserCreateForm $form): User
    {
        $user = User::create($form);

        $this->transaction->wrap(function () use ($user, $form) {
            $user->assignStat();

            if (!empty($form->subjects)) {
                $this->assignSubjects($user, $form->subjects);
            }

            if (!empty($form->sections)) {
                $this->assignSections($user, $form->sections);
            }

            $this->assignPrograms($user, $form);

            if (!empty($form->competencies)) {
                $this->assignCompetencies($user, $form->competencies);
            }

            $this->users->save($user);

            $form->user = $user;
            $this->manageCase->saveCompetencyProfile($form);
            $this->roles->assign($user->user_uuid, $user->role);
        });

        return $user;
    }

    public function confirm($token): void
    {
        if (empty($token)) {
            throw new DomainException('Empty confirm token.');
        }

        /** @var User $user */
        $user = $this->users->getByVerificationToken($token);
        $user->confirmSignup();
        $this->users->save($user);
    }

    public function assignSubjects(User $user, $subjects)
    {
        if (!$user->isNewRecord) {
            $user->revokeSubjects();
            $this->users->save($user);
        }

        foreach (($subjects ? : []) as $subject) {
            if (!$subject) continue;
            $user->assignSubject($subject);
        }
    }

    public function assignSections(User $user, $sections)
    {
        if (!$user->isNewRecord) {
            $user->revokeSections();
            $this->users->save($user);
        }

        foreach (($sections ? : []) as $section) {
            if (!$section) continue;
            $user->assignSection($section);
        }
    }

    public function assignPrograms(User $user, UserCreateForm $form)
    {
        if (is_array($form->programs) && count($form->programs) > 0 && $form->role === Rbac::ROLE_EMP) {
            foreach ($form->programs as $program) {
                $program_uuid = ArrayHelper::getValue($program, 'program_uuid', '');
                $session = ArrayHelper::getValue($program, 'session', 0);
                $enable = ArrayHelper::getValue($program, 'enable', 'false');

                if ($enable == 'true') {
                    $user->assignProgram($program_uuid, $session);
                }
            }
        } else {
            $program_uuid = ArrayHelper::getValue(Program::UuidByRole(), $user->role);
            if ($program_uuid) {
                $user->assignProgram($program_uuid);
            }
        }
    }

    public function assignCompetencies(User $user, $competencies)
    {
        if (!$user->isNewRecord) {
            $user->revokeCompetencies();
            $this->users->save($user);
        }

        foreach (($competencies ? : []) as $competency) {
            if (!$competency) continue;
            $user->assignCompetence($competency);
        }
    }
}
