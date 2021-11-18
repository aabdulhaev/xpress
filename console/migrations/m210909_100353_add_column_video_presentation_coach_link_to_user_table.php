<?php

use yii\db\Migration;

/**
 * Class m210909_100353_add_column_video_presentation_coach_link_to_user_table
 */
class m210909_100353_add_column_video_presentation_coach_link_to_user_table extends Migration
{
    public $tableName = '{{%user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'video_presentation_coach_link', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'video_presentation_coach_link');
    }
}
