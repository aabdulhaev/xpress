<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%training_session}}`.
 */
class m210811_083341_add_move_request_reject_comment_column_to_training_session_table extends Migration
{
    public $tableName = '{{%training_session}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'move_request_reject_comment', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'move_request_reject_comment');
    }
}
