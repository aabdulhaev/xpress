<?php

$bbbDomain = 'xpress-bbb.loc';
$frontDomain = 'xpress.loc';
$apiDomain = 'api-xpress.loc';

return [
    'adminEmail'                    => 'cmdo-unikum@pfur.ru',
    'supportEmail'                  => 'support@example.com',
    'senderEmail'                   => 'noreply@example.com',
    'senderName'                    => 'Example.com mailer',
    'senderPhone'                   => '8 (800) 333 13 24',
    'user.passwordResetTokenExpire' => 1800,
    'user.accessTokenExpire'        => 3600 * 24 * 7,
    'user.refreshTokenExpire'       => 3600 * 24 * 30,
    'bsVersion'                     => '4.x',
    'JWT_SECRET' => 'HDHH2DUHhSQ:C9wjxh&gUH*NALCOL9exuHEYC',

    'frontHost' => 'https://' . $frontDomain,
    'frontDomain' => $frontDomain,

    'confirmEmailFrontLink' => 'confirm/email',
    'confirmEventFrontLink' => 'confirm/event',
    'declineEventFrontLink' => 'decline/event',
    'confirmRelFrontLink' => 'confirm/rel',
    'declineRelFrontLink' => 'decline/rel',
    'resetPasswordFrontLink' => 'reset/password',
    'profileFrontLink' => 'main',
    'confirmGroupMeetingLink' => 'confirm/webinar',
    'joinGroupMeetingLink' => 'join/webinar',

    'storagePath' => dirname(__DIR__, 2) . '/api/web/static',
    'storageHostInfo' => 'https://' . $apiDomain . '/static',

    'clearDeclineTime' => 60 * 30,

    'BBB_SECRET' => 'nVnzCdnBM5zdFEgiwgbQKAFxuw9GrZE269CoOL5Oak',
    'BBB_SERVER_BASE_URL' => $bbbDomain . '/bigbluebutton/',
    'BBB_MEETING_BASE_URL' => $bbbDomain . '/',
    'BBB_HOOK_URL' => 'https://'.$apiDomain.'/v1/hook/',
    'BBB_LOGOUT_URL' => 'https://'.$frontDomain.'/rating',

    'BBB_MAX_PARTICIPANTS' => 100,

    'bccEmails'=>['support@xpress.loc'=>'Support'],
    'logoHash'=>'data:image/png;base64,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',
    'logoPath'=>'https://'.$apiDomain.'/images/logo.png',

    'expiredSessionCancelTime' => 60 * 30,
];
