<?php

use yii\rest\UrlRule;

$uuidToken = '<uuid:[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>';

return [

    // Client
    [
        'class' => UrlRule::class,
        'controller' => 'v1/client',
        'pluralize' => false,
        'tokens' => [
            '<id>' => str_replace('uuid', 'id', $uuidToken)
        ],
        'patterns' => [
            'GET view/<id>' => 'view',
            'OPTIONS view/<id>' => 'view',
            'POST create' => 'create',
            'OPTIONS create' => 'create',
            'PATCH update/<id>' => 'update',
            'OPTIONS update/<id>' => 'update'
        ]
    ],

    // User
    [
        'class' => UrlRule::class,
        'controller' => 'v1/user',
        'pluralize' => false,
        'tokens' => [
            '<id>' => str_replace('uuid', 'id', $uuidToken)
        ],
        'patterns' => [
            'GET' => 'index',
            'OPTIONS' => 'index',
            'GET  view/<id>' => 'view',
            'OPTIONS  view/<id>' => 'view',
            'POST  create' => 'create',
            'OPTIONS  create' => 'create',
            'POST  update/<id>' => 'update',
            'OPTIONS  update/<id>' => 'update',
            'DELETE  delete/<id>' => 'delete',
            'OPTIONS  delete/<id>' => 'delete',
            'GET roles' => 'roles',
            'OPTIONS roles' => 'roles',
            'POST set-role' => 'set-role',
            'OPTIONS set-role' => 'set-role',
            'PATCH password' => 'password',
            'OPTIONS password' => 'password',
            'PATCH  change-status/<id>' => 'change-status',
            'OPTIONS  change-status/<id>' => 'change-status',
        ]
    ],

    // Profile
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/profile',
        'patterns' => [
            'GET subjects' => 'subjects',
            'GET competencies' => 'competencies',
            'GET all-subjects' => 'all-subjects',
            'GET all-competencies' => 'all-competencies',
            'GET all-sections' => 'all-sections',
            'PATCH  password' => 'password',
            'OPTIONS  password' => 'password',
            'POST  change-avatar' => 'change-avatar',
            'OPTIONS  change-avatar' => 'change-avatar',
            'POST  assign-subject' => 'assign-subject',
            'OPTIONS  assign-subject' => 'assign-subject',
            'POST  upload-video-presentation-coach' => 'upload-video-presentation-coach',
            'OPTIONS  upload-video-presentation-coach' => 'upload-video-presentation-coach',
            'POST  delete-video-presentation-coach' => 'delete-video-presentation-coach',
            'OPTIONS  delete-video-presentation-coach' => 'delete-video-presentation-coach'
        ]
    ],

    // Auth
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/auth',
        'patterns' => [
            'POST  confirm/<token>' => 'confirm',
            'OPTIONS  confirm/<token>' => 'confirm',
            'POST  login' => 'login',
            'OPTIONS  login' => 'login',
        ]
    ],

    // Request
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/request',
        'tokens' => [
            '<id>' => str_replace('uuid', 'id', $uuidToken)
        ],
        'patterns' => [
            'GET  approve/<id>' => 'approve',
            'GET  decline/<id>' => 'decline',
            'GET  index' => 'index',
            'GET  view/<id>' => 'view'
        ]
    ],

    // Employee
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/employee',
        'tokens' => [
            '<user_uuid>' => str_replace('uuid', 'user_uuid', $uuidToken)
        ],
        'patterns' => [
            'GET approved' => 'approved',
            'OPTIONS approved' => 'approved',
            'GET not-approved' => 'not-approved',
            'OPTIONS not-approved' => 'not-approved',
            'GET connected' => 'connected',
            'OPTIONS connected' => 'connected',
            'GET unconnected' => 'unconnected',
            'OPTIONS unconnected' => 'unconnected',
            'POST  contact/<user_uuid>' => 'contact',
            'OPTIONS  contact/<user_uuid>' => 'contact',
            'POST  invite/<user_uuid>' => 'invite',
            'OPTIONS  invite/<user_uuid>' => 'invite',
            'PATCH  approve-connect/<user_uuid>' => 'approve-connect',
            'OPTIONS  approve-connect/<user_uuid>' => 'approve-connect',
            'PATCH  decline-connect/<user_uuid>' => 'decline-connect',
            'OPTIONS  decline-connect/<user_uuid>' => 'decline-connect',
            'PATCH  cancel-connect/<user_uuid>' => 'cancel-connect',
            'OPTIONS  cancel-connect/<user_uuid>' => 'cancel-connect',
            'POST  change-status/<user_uuid>' => 'change-status',
            'OPTIONS  change-status/<user_uuid>' => 'change-status',
            'PATCH  program/<user_uuid>' => 'program',
            'OPTIONS  program/<user_uuid>' => 'program',
            'PATCH  update/<user_uuid>' => 'update',
            'OPTIONS  update/<user_uuid>' => 'update',
        ]
    ],

    // Mentor
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/mentor',
        'tokens' => [
            '<user_uuid>' => str_replace('uuid', 'user_uuid', $uuidToken)
        ],
        'patterns' => [
            'GET approved' => 'approved',
            'OPTIONS approved' => 'approved',
            'GET not-approved' => 'not-approved',
            'OPTIONS not-approved' => 'not-approved',
            'GET connected' => 'connected',
            'OPTIONS connected' => 'connected',
            'GET unconnected' => 'unconnected',
            'OPTIONS unconnected' => 'unconnected',
            'POST  create-request/<user_uuid>' => 'create-request',
            'OPTIONS  create-request/<user_uuid>' => 'create-request',
            'POST  invite/<user_uuid>' => 'invite',
            'OPTIONS  invite/<user_uuid>' => 'invite',
            'POST  contact/<user_uuid>' => 'contact',
            'OPTIONS  contact/<user_uuid>' => 'contact',
            'POST  change-status/<user_uuid>' => 'change-status',
            'OPTIONS  change-status/<user_uuid>' => 'change-status',
            'PATCH  approve-connect/<user_uuid>' => 'approve-connect',
            'PATCH  decline-connect/<user_uuid>' => 'decline-connect',
            'OPTIONS  decline-connect/<user_uuid>' => 'decline-connect',
            'POST  connect' => 'connect',
        ]
    ],

    // Coach
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/coach',
        'tokens' => [
            '<user_uuid>' => str_replace('uuid', 'user_uuid', $uuidToken)
        ],
        'patterns' => [
            'GET index' => 'index',
            'OPTIONS index' => 'index',
            'GET approved' => 'approved',
            'OPTIONS approved' => 'approved',
            'GET connected' => 'connected',
            'OPTIONS connected' => 'connected',
            'GET unconnected' => 'unconnected',
            'OPTIONS unconnected' => 'unconnected',
            'PATCH  approve-connect/<user_uuid>' => 'approve-connect',
            'OPTIONS  approve-connect/<user_uuid>' => 'approve-connect',
            'PATCH  decline-connect/<user_uuid>' => 'decline-connect',
            'OPTIONS  decline-connect/<user_uuid>' => 'decline-connect',
            'POST  create-request/<user_uuid>' => 'create-request',
            'OPTIONS  create-request/<user_uuid>' => 'create-request',
            'POST  contact/<user_uuid>' => 'contact',
            'OPTIONS  contact/<user_uuid>' => 'contact',
            'GET newest' => 'newest',
            'OPTIONS newest' => 'newest',
            'POST  add' => 'add',
            'OPTIONS  add' => 'add',
            'POST  remove' => 'remove',
            'OPTIONS  remove' => 'remove',
            'POST  invite/<user_uuid>' => 'invite',
            'OPTIONS  invite/<user_uuid>' => 'invite',
            'POST  connect' => 'connect',
            'OPTIONS  connect' => 'connect'
        ]
    ],

    // Planning
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/planning',
        'tokens' => [
            '<training_uuid>' => str_replace('uuid', 'training_uuid', $uuidToken)
        ],
        'patterns' => [
            'PATCH  cancel/<training_uuid>' => 'cancel',
            'OPTIONS  cancel/<training_uuid>' => 'cancel',
            'PATCH  move/<training_uuid>' => 'move',
            'OPTIONS  move/<training_uuid>' => 'move',
            'PATCH  reject-move-request/<training_uuid>' => 'reject-move-request',
            'OPTIONS  reject-move-request/<training_uuid>' => 'reject-move-request',
            'POST  rate/<training_uuid>' => 'rate',
            'OPTIONS  rate/<training_uuid>' => 'rate',
            'PATCH  take/<training_uuid>' => 'take',
            'OPTIONS  take/<training_uuid>' => 'take',
            'PATCH  confirm/<training_uuid>' => 'confirm',
            'OPTIONS  confirm/<training_uuid>' => 'confirm',
            'POST  create' => 'create',
            'OPTIONS  create' => 'create'
        ]
    ],

    // Signup
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/signup',
        'tokens' => [
            '<token>' => str_replace('uuid', 'token', $uuidToken)
        ],
        'patterns' => [
            'GET confirm/<token>' => 'confirm',
            'OPTIONS confirm/<token>' => 'confirm',
            'POST  employee' => 'employee',
            'OPTIONS  employee' => 'employee',
            'POST  mentor' => 'mentor',
            'OPTIONS  mentor' => 'mentor',
        ]
    ],

    // Meeting
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/meeting',
        'tokens' => [
            '<training_uuid>' => str_replace('uuid', 'training_uuid', $uuidToken),
            '<meeting_uuid>' => str_replace('uuid', 'meeting_uuid', $uuidToken)
        ],
        'patterns' => [
            'GET index' => 'index',
            'OPTIONS index' => 'index',
            'GET view/<meeting_uuid>' => 'view',
            'OPTIONS view/<meeting_uuid>' => 'view',
            'POST start' => 'start',
            'OPTIONS start' => 'start',
            'GET join/<training_uuid>' => 'join',
            'OPTIONS join/<training_uuid>' => 'join',
            'POST group-create' => 'group-create',
            'OPTIONS group-create' => 'group-create',
            'PATCH group-update/<meeting_uuid>' => 'group-update',
            'OPTIONS group-update/<meeting_uuid>' => 'group-update',
            'DELETE group-delete/<meeting_uuid>' => 'group-delete',
            'OPTIONS group-delete/<meeting_uuid>' => 'group-delete',
            'GET group-join/<meeting_uuid>' => 'group-join',
            'OPTIONS group-join/<meeting_uuid>' => 'group-join',
            'POST make-confirm' => 'make-confirm',
            'OPTIONS make-confirm' => 'make-confirm',
            'GET check-confirm/<token>' => 'check-confirm',
            'OPTIONS check-confirm/<token>' => 'check-confirm',
            'GET check-email/<email>' => 'check-email',
            'OPTIONS check-email/<email>' => 'check-email',
        ]
    ],

    // Hook
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/hook',
        'tokens' => [
            '<meetingId>' => str_replace('uuid', 'meetingId', $uuidToken)
        ],
        'patterns' => [
            'GET end/<meetingId>' => 'end',
            'OPTIONS end/<meetingId>' => 'end',
            'GET end-meeting/<meetingId>' => 'end-meeting',
            'OPTIONS end-meeting/<meetingId>' => 'end-meeting',
        ]
    ],

    // Competency profile
    [
        'class' => UrlRule::class,
        'controller' => 'v1/competency-profile',
        'pluralize' => false,
        'tokens' => [
            '<id>' => str_replace('uuid', 'id', $uuidToken)
        ],
        'patterns' => [
            'DELETE delete/<id>' => 'delete',
            'OPTIONS delete/<id>' => 'delete',
        ]
    ],

    // Contact
    [
        'class' => UrlRule::class,
        'pluralize' => false,
        'controller' => 'v1/contact',
        'patterns' => [
            'POST' => 'send',
            'OPTIONS' => 'send',
        ]
    ],

    //Knowledge library
    [
        'class' => UrlRule::class,
        'controller' => 'v1/material',
        'pluralize' => false,
        'tokens' => [
            '<id>' => str_replace('uuid', 'id', $uuidToken)
        ],
        'patterns' => [
            'GET' => 'index',
            'OPTIONS' => 'index',
            'PATCH learned/<id>' => 'learned',
            'OPTIONS learned/<id>' => 'learned',
            'PATCH elected/<id>' => 'elected',
            'OPTIONS elected/<id>' => 'elected',
            'POST bind/<id>' => 'bind',
            'OPTIONS bind/<id>' => 'bind',
            'POST unbind/<id>' => 'unbind',
            'OPTIONS unbind/<id>' => 'unbind',
            'GET view/<id>' => 'view',
            'OPTIONS view/<id>' => 'view',
            'POST create' => 'create',
            'OPTIONS create' => 'create',
            'PATCH update/<id>' => 'update',
            'OPTIONS update/<id>' => 'update',
            'DELETE delete/<id>' => 'delete',
            'OPTIONS delete/<id>' => 'delete',
            'GET moderating/<id>' => 'moderating',
            'OPTIONS moderating/<id>' => 'moderating',
            'PATCH approve/<id>' => 'approve',
            'OPTIONS approve/<id>' => 'approve',
            'PATCH decline/<id>' => 'decline',
            'OPTIONS decline/<id>' => 'decline',
            'GET all-themes/<id>' => 'all-themes',
            'OPTIONS all-themes/<id>' => 'all-themes',
            'GET all-tags/<id>' => 'all-tags',
            'OPTIONS all-tags/<id>' => 'all-tags',
            'GET all-languages/<id>' => 'all-languages',
            'OPTIONS all-languages/<id>' => 'all-languages',
        ]
    ],

    //Directory
    [
        'class' => UrlRule::class,
        'controller' => 'v1/directory',
        'pluralize' => false,
        'tokens' => [
            '<directory>' => '<directory:\w+>',
            '<uuid>' => $uuidToken
        ],
        'patterns' => [
            'GET <directory>' => 'index',
            'OPTIONS <directory>' => 'index',
            'GET view/<directory>/<uuid>' => 'view',
            'OPTIONS view/<directory>/<uuid>' => 'view',
            'POST create/<directory>' => 'create',
            'OPTIONS create/<directory>' => 'create',
            'PATCH update/<directory>/<uuid>' => 'update',
            'OPTIONS update/<directory>/<uuid>' => 'update',
            'DELETE delete/<directory>/<uuid>' => 'delete',
            'OPTIONS delete/<directory>/<uuid>' => 'delete',
        ]
    ],

    //Google Auth
    [
        'class' => UrlRule::class,
        'controller' => 'v1/google',
        'pluralize' => false,
        'patterns' => [
            'GET auth' => 'auth',
            'OPTIONS auth' => 'auth',
            'GET process-code' => 'process-code',
            'OPTIONS process-code' => 'process-code',
        ]
    ],
];
