<?php

namespace common\access;

class Rbac
{
    public const ROLE_ADMIN = 'account-manager';
    public const ROLE_HR = 'hr-admin';
    public const ROLE_EMP = 'employee';
    public const ROLE_COACH = 'coach';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_MODERATOR = 'moderator';


    public static function roles() : array
    {
        return [
            self::ROLE_ADMIN => 'account-manager',
            self::ROLE_HR => 'hr-admin',
            self::ROLE_EMP => 'employee',
            self::ROLE_COACH => 'coach',
            self::ROLE_MENTOR => 'mentor',
            self::ROLE_MODERATOR => 'moderator',
        ];
    }

    public static function rolesTitle() : array
    {
        return [
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_HR => 'HR',
            self::ROLE_EMP => 'Сотрудник',
            self::ROLE_COACH => 'Коуч',
            self::ROLE_MENTOR => 'Ментор',
            self::ROLE_MODERATOR => 'Модератор',
        ];
    }
}
