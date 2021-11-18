<?php

namespace common\bootstrap;

use common\dispatchers\AsyncEventDispatcher;
use common\dispatchers\DeferredEventDispatcher;
use common\dispatchers\EventDispatcher;
use common\dispatchers\SimpleEventDispatcher;
use common\jobs\AsyncEventJobHandler;
use common\models\events\AdminCreateGroupMeeting;
use common\models\events\CancelMeetingNotification;
use common\models\events\CancelSession;
use common\models\events\CancelSessionNotification;
use common\models\events\ConfirmSession;
use common\models\events\EmailNotificationAtStartMeeting;
use common\models\events\EmailNotificationBeforeStartMeeting;
use common\models\events\EmployeeCreateConnect;
use common\models\events\EmployeeCreateSessionPlanning;
use common\models\events\EmployeeUnconnectMentor;
use common\models\events\EmployeeUnconnectMentorForEmployee;
use common\models\events\MentorApproveConnectEmployee;
use common\models\events\MentorCancelEmployee;
use common\models\events\MentorCreateMeeting;
use common\models\events\MentorUnconnectEmployee;
use common\models\events\MentorUnconnectEmployeeForMentor;
use common\models\events\MoveMeetingNotification;
use common\models\events\MoveSessionRequest;
use common\models\events\NotificationPlannedSession;
use common\models\events\PasswordResetRequest;
use common\models\events\RejectedMoveSessionRequest;
use common\models\events\Stats;
use common\models\events\UserConnect;
use common\models\events\UserContact;
use common\models\events\UserInvite;
use common\models\events\UserManyConnections;
use common\models\events\UserSessionRating;
use common\models\events\UserSignUpRequested;
use common\models\listeners\AdminCreateGroupMeetingListener;
use common\models\listeners\CancelMeetingNotificationListener;
use common\models\listeners\CancelSessionForGoogleListener;
use common\models\listeners\CancelSessionListener;
use common\models\listeners\CancelSessionNotificationListener;
use common\models\listeners\ConfirmSessionForGoogleListener;
use common\models\listeners\ConfirmSessionListener;
use common\models\listeners\EmailNotificationAtStartMeetingListener;
use common\models\listeners\EmailNotificationBeforeStartMeetingListener;
use common\models\listeners\EmployeeCreateConnectListener;
use common\models\listeners\EmployeeCreateSessionPlanningListener;
use common\models\listeners\EmployeeUnconnectMentorForEmployeeListener;
use common\models\listeners\EmployeeUnconnectMentorListener;
use common\models\listeners\MentorApproveConnectEmployeeListener;
use common\models\listeners\MentorCancelEmployeeListener;
use common\models\listeners\MentorCreateMeetingListener;
use common\models\listeners\MentorUnconnectEmployeeForMentorListener;
use common\models\listeners\MentorUnconnectEmployeeListener;
use common\models\listeners\MoveMeetingNotificationListener;
use common\models\listeners\MoveSessionRequestListener;
use common\models\listeners\NotificationPlannedSessionListener;
use common\models\listeners\PasswordRequestListener;
use common\models\listeners\RejectedMoveSessionRequestListener;
use common\models\listeners\StatListener;
use common\models\listeners\UserConnectListener;
use common\models\listeners\UserContactListener;
use common\models\listeners\UserInviteListener;
use common\models\listeners\UserManyConnectionsListener;
use common\models\listeners\UserSessionRatingListener;
use common\models\listeners\UserSignupRequestedListener;
use Yii;
use yii\base\BootstrapInterface;
use yii\di\Container;
use yii\di\Instance;
use yii\mail\MailerInterface;
use yii\queue\Queue;
use yii\rbac\ManagerInterface;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = Yii::$container;

        $container->setSingleton(Queue::class, static function () use ($app) {
            return $app->get('queue');
        });

        $container->setSingleton(ManagerInterface::class, static function () use ($app) {
            return $app->authManager;
        });

        $container->setSingleton(MailerInterface::class, static function () use ($app) {
            return $app->mailer;
        });

        $container->setSingleton(EventDispatcher::class, DeferredEventDispatcher::class);

        $container->setSingleton(DeferredEventDispatcher::class, static function (Container $container) {
            return new DeferredEventDispatcher(new AsyncEventDispatcher($container->get(Queue::class)));
        });

        $container->setSingleton(SimpleEventDispatcher::class, static function (Container $container) {
            return new SimpleEventDispatcher($container, [
                UserSignUpRequested::class => [UserSignupRequestedListener::class],
                UserManyConnections::class => [UserManyConnectionsListener::class],
                UserConnect::class => [UserConnectListener::class],
                UserContact::class => [UserContactListener::class],
                UserInvite::class => [UserInviteListener::class],
                EmployeeCreateConnect::class => [EmployeeCreateConnectListener::class],
                PasswordResetRequest::class => [PasswordRequestListener::class],
                EmployeeUnconnectMentor::class => [EmployeeUnconnectMentorListener::class],
                EmployeeUnconnectMentorForEmployee::class => [EmployeeUnconnectMentorForEmployeeListener::class],
                MentorUnconnectEmployee::class => [MentorUnconnectEmployeeListener::class],
                MentorUnconnectEmployeeForMentor::class => [MentorUnconnectEmployeeForMentorListener::class],
                MentorCreateMeeting::class => [MentorCreateMeetingListener::class],
                AdminCreateGroupMeeting::class => [AdminCreateGroupMeetingListener::class],
                EmailNotificationBeforeStartMeeting::class => [EmailNotificationBeforeStartMeetingListener::class],
                EmailNotificationAtStartMeeting::class => [EmailNotificationAtStartMeetingListener::class],
                CancelMeetingNotification::class => [CancelMeetingNotificationListener::class],
                MoveMeetingNotification::class => [MoveMeetingNotificationListener::class],
                Stats::class => [StatListener::class],
                CancelSession::class => [CancelSessionListener::class, CancelSessionForGoogleListener::class],
                MentorApproveConnectEmployee::class => [MentorApproveConnectEmployeeListener::class],
                EmployeeCreateSessionPlanning::class => [EmployeeCreateSessionPlanningListener::class],
                MentorCancelEmployee::class => [MentorCancelEmployeeListener::class],
                ConfirmSession::class => [ConfirmSessionListener::class, ConfirmSessionForGoogleListener::class],
                MoveSessionRequest::class => [MoveSessionRequestListener::class],
                RejectedMoveSessionRequest::class => [RejectedMoveSessionRequestListener::class],
                CancelSessionNotification::class => [CancelSessionNotificationListener::class],
                UserSessionRating::class => [UserSessionRatingListener::class],
                NotificationPlannedSession::class => [NotificationPlannedSessionListener::class],
            ]);
        });

        $container->setSingleton(AsyncEventJobHandler::class, [], [
            Instance::of(SimpleEventDispatcher::class)
        ]);
    }
}
