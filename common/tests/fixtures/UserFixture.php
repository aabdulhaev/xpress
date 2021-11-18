<?php

namespace common\tests\fixtures;

use common\access\Rbac;
use common\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public const PASSWORD = 'password_0';
    public const PASSWORD_HASH = '$2y$13$EjaPFBnZOQsHdGuHI.xvhuDp1fHpo8hKRSk6yshqa9c5EG8s3C3lO';

    public const ADMIN_AUTH_1 = [
        'r' => Rbac::ROLE_ADMIN,
        'l' => 'admin1@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb14784-18ec-6a00-8c0e-d0f21dc4ef1a'
    ];
    public const ADMIN_AUTH_2 = [
        'r' => Rbac::ROLE_ADMIN,
        'l' => 'admin2@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da73-82b0-6720-2256-bb8544bf9c6d'
    ];
    public const ADMIN_AUTH_3 = [
        'r' => Rbac::ROLE_ADMIN,
        'l' => 'admin3@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da74-defd-67c0-3567-6a2ce1c044ef'
    ];

    public const HR_AUTH_1 = [
        'r' => Rbac::ROLE_HR,
        'l' => 'hr1@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb14784-18ef-6110-30fc-6cfd67b18d05'
    ];
    public const HR_AUTH_2 = [
        'r' => Rbac::ROLE_HR,
        'l' => 'hr2@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb24f2a-b423-62e0-b8a9-c9efa636801f'
    ];
    public const HR_AUTH_3 = [
        'r' => Rbac::ROLE_HR,
        'l' => 'hr3@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da77-5066-6771-6f12-c863c91537d3'
    ];
    public const HR_AUTH_4 = [
        'r' => Rbac::ROLE_HR,
        'l' => 'hr4@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241b9-fcb9-6b90-a7b9-4bb2092d655e'
    ];

    public const COACH_AUTH_1 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa1@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb17a02-82fa-6771-592c-aca9f07496b6'
    ];
    public const COACH_AUTH_2 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa2@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da7e-4428-6242-d849-4b989b3de22a'
    ];
    public const COACH_AUTH_3 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa3@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da7e-4428-6243-12ed-6de36a0ba985'
    ];
    public const COACH_AUTH_4 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa4@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb2a4dd-4bb1-6190-5fdf-8ce78fbede0f'
    ];
    public const COACH_AUTH_5 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa5@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb2a4dd-4bb5-6fb0-dd56-de7357a68b67'
    ];
    public const COACH_AUTH_6 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa6@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb2a4dd-4bb5-6fb1-9127-1a3ed4b43e17'
    ];
    public const COACH_AUTH_7 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa7@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e684-68ce-855e-0242ac1a0004'
    ];
    public const COACH_AUTH_8 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa8@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e69f-60e8-8e67-0242ac1a0004'
    ];
    public const COACH_AUTH_9 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa9@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e69f-649e-8537-0242ac1a0004'
    ];
    public const COACH_AUTH_10 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa10@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e69f-66ce-9b95-0242ac1a0004'
    ];
    public const COACH_AUTH_11 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa11@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e69f-68fe-8ed7-0242ac1a0004'
    ];
    public const COACH_AUTH_12 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa12@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e69f-6b24-a31c-0242ac1a0004'
    ];
    public const COACH_AUTH_13 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa13@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e69f-6d40-8a9c-0242ac1a0004'
    ];
    public const COACH_AUTH_14 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa14@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e69f-6f66-ba77-0242ac1a0004'
    ];
    public const COACH_AUTH_15 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa15@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a0-618c-8e59-0242ac1a0004'
    ];
    public const COACH_AUTH_16 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa16@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a0-63a8-99a6-0242ac1a0004'
    ];
    public const COACH_AUTH_17 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa17@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a0-65c4-95cc-0242ac1a0004'
    ];
    public const COACH_AUTH_18 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa18@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a0-6920-9ca7-0242ac1a0004'
    ];
    public const COACH_AUTH_19 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa19@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a0-6c0e-829b-0242ac1a0004'
    ];
    public const COACH_AUTH_20 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa20@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a0-6e3e-80df-0242ac1a0004'
    ];
    public const COACH_AUTH_21 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa21@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a1-61cc-94ed-0242ac1a0004'
    ];
    public const COACH_AUTH_22 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa22@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a1-63f2-9bc9-0242ac1a0004'
    ];
    public const COACH_AUTH_23 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa23@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a1-6604-9e2b-0242ac1a0004'
    ];
    public const COACH_AUTH_24 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa24@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a1-682a-8f25-0242ac1a0004'
    ];
    public const COACH_AUTH_25 = [
        'r' => Rbac::ROLE_COACH,
        'l' => 'coa25@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1ebd2850-e6a1-6a46-ac12-0242ac1a0004'
    ];

    public const EMP_AUTH_1 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp1@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb150ad-eb40-6c90-d8a5-ce057c5995d2'
    ];
    public const EMP_AUTH_2 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp2@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da77-5066-6770-8c24-da379b6e1540'
    ];
    public const EMP_AUTH_3 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp3@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb15371-eee7-6260-05d9-9ca86172ab85'
    ];
    public const EMP_AUTH_4 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp10@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26c8-6c30-d39b-c13212ed2401'
    ];
    public const EMP_AUTH_5 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp11@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26c8-6c31-86f2-b947907df771'
    ];
    public const EMP_AUTH_6 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp12@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6340-c841-c7d12a30f4ed'
    ];
    public const EMP_AUTH_7 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp13@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6341-29af-a59295f19001'
    ];
    public const EMP_AUTH_8 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp14@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6342-dc19-7f00ae401869'
    ];
    public const EMP_AUTH_9 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp15@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6343-844e-f64655e90f14'
    ];
    public const EMP_AUTH_10 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp16@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6344-454c-40631224eaa4'
    ];
    public const EMP_AUTH_11 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp17@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6345-2806-55892633fe3c'
    ];
    public const EMP_AUTH_12 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp18@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6346-03be-27ea9f8cf705'
    ];
    public const EMP_AUTH_13 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp19@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241cc-26cb-6347-6be2-44d83bbe631e'
    ];
    public const EMP_AUTH_14 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp20@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dcf-6700-2908-0a99025557d5'
    ];
    public const EMP_AUTH_15 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp21@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dcf-6701-bf73-31b36f46c869'
    ];
    public const EMP_AUTH_16 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp22@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e10-cab5-b3fee1f9def4'
    ];
    public const EMP_AUTH_17 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp23@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e11-7202-6e41c022b8cf'
    ];
    public const EMP_AUTH_18 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp24@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e12-350c-a2febae4f5bb'
    ];
    public const EMP_AUTH_19 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp25@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e13-0c46-ffdd8ffd901e'
    ];
    public const EMP_AUTH_20 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp26@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e14-6ef5-d800154f3ab4'
    ];
    public const EMP_AUTH_21 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp27@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e15-85f9-c19af845177f'
    ];
    public const EMP_AUTH_22 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp28@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e16-7d33-467015174fd3'
    ];
    public const EMP_AUTH_23 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp29@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd1-6e17-8842-e7c40840b437'
    ];
    public const EMP_AUTH_24 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp30@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd4-6520-454a-ac7ff9eaf276'
    ];
    public const EMP_AUTH_25 = [
        'r' => Rbac::ROLE_EMP,
        'l' => 'emp31@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d3-4dd4-6521-30ea-16dd747a14f4'
    ];

    public const MENT_AUTH_1 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment1@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb17a02-82fa-6770-f65f-73f53aee6c4f'
    ];
    public const MENT_AUTH_2 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment2@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da7e-4428-6240-f562-bce2efc5f212'
    ];
    public const MENT_AUTH_3 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment3@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb1da7e-4428-6241-e0a0-848a4a331362'
    ];
    public const MENT_AUTH_4 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment10@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3857-6630-e7d5-e6b8fe32eded'
    ];
    public const MENT_AUTH_5 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment11@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3857-6631-8f6e-167a574b6604'
    ];
    public const MENT_AUTH_6 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment12@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d40-fb41-5220ec0db05c'
    ];
    public const MENT_AUTH_7 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment13@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d41-0ab3-471b012f8430'
    ];
    public const MENT_AUTH_8 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment14@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d42-123c-78f37c00ec46'
    ];
    public const MENT_AUTH_9 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment15@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d43-76ae-6f2a9f203a9b'
    ];
    public const MENT_AUTH_10 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment16@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d44-d970-ea4a20b5b495'
    ];
    public const MENT_AUTH_11 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment17@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d45-ab2b-9cd1bbfb83ae'
    ];
    public const MENT_AUTH_12 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment18@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d46-f43e-00526bf238ed'
    ];
    public const MENT_AUTH_13 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment19@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d47-8c20-bbff689dbc6e'
    ];
    public const MENT_AUTH_14 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment20@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d48-ebbb-3a7c2f589318'
    ];
    public const MENT_AUTH_15 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment21@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d49-83f9-3bb41b5b9451'
    ];
    public const MENT_AUTH_16 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment22@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-3859-6d4a-4461-83cb14fa4ab6'
    ];
    public const MENT_AUTH_17 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment23@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6450-e3f9-c9c27bbac818'
    ];
    public const MENT_AUTH_18 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment24@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6451-99de-722e15fa56aa'
    ];
    public const MENT_AUTH_19 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment25@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6452-9b67-80a9a67767dc'
    ];
    public const MENT_AUTH_20 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment26@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6453-3519-80df60e579ee'
    ];
    public const MENT_AUTH_21 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment27@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6454-7727-69d0686d6bb9'
    ];
    public const MENT_AUTH_22 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment28@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6455-90cc-6aa398ce9834'
    ];
    public const MENT_AUTH_23 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment29@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6456-14d1-cd83f0bf3e99'
    ];
    public const MENT_AUTH_24 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment30@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6457-d79d-1e6cefae741d'
    ];
    public const MENT_AUTH_25 = [
        'r' => Rbac::ROLE_MENTOR,
        'l' => 'ment31@xpress.loc',
        'p' => self::PASSWORD,
        'id' => '1eb241d7-385c-6458-b130-e9e567ee7650'
    ];

    public $modelClass = User::class;
    public $depends = [
        AuthAssignmentFixture::class,
        ClientFixture::class,
    ];
}
