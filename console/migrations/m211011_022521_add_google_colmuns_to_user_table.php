<?php

use common\models\User;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m211011_022521_add_google_colmuns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(User::tableName(), 'google_access_token', $this->string());
        $this->addColumn(User::tableName(), 'google_refresh_token', $this->string());
        $this->addColumn(User::tableName(), 'google_token_created', $this->integer());
        $this->addColumn(User::tableName(), 'google_expires_in', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(User::tableName(), 'google_access_token');
        $this->dropColumn(User::tableName(), 'google_refresh_token');
        $this->dropColumn(User::tableName(), 'google_token_created');
        $this->dropColumn(User::tableName(), 'google_expires_in');
    }

}
