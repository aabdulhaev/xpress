<?php
/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 14.04.2021
 */


namespace common\forms;


use common\models\User;
use yii\base\Model;

/**
 *  Support Request form
 */
class SupportRequestForm extends Model
{
    public $text;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return[
            [['text'], 'filter', 'filter'=>'strip_tags'],
            [['text'], 'filter', 'filter'=>'trim'],
            [['text'], 'required'],
            [['text'], 'string','max' => 10000],
        ];
    }

    /**
     * @param User $sender
     * @return bool
     */
    public function sendEmail(User $sender): bool
    {
        return \Yii::$app->mailer->compose(
            ['html' => 'support-request-html', 'text' => 'support-request-text'],
            [
                'sender' => $sender,
                'text' => $this->text
            ]
        )->setTo(\Yii::$app->params['supportEmail'])
            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
            ->setSubject('Support Request')
            ->send();
    }
    /**
     * @param User $sender
     * @return bool
     */
    public function sendThanksEmail(User $sender): bool
    {
        return \Yii::$app->mailer->compose(
            ['html' => 'thanks-support-request-html', 'text' => 'thanks-support-request-text'],
            [
                'sender' => $sender,
                'text' => $this->text
            ]
        )->setTo($sender->email)
            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
            ->setSubject('Спасибо за обращение в службу поддержки!')
            ->send();
    }


}