<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m210324_154506_add_practice_hours_column_content_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'practice_hours', $this->string());
        $this->addColumn('{{%user}}', 'content', $this->text());
        $this->addColumn('{{%user}}', 'languages', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'practice_hours');
        $this->dropColumn('{{%user}}', 'content');
        $this->dropColumn('{{%user}}', 'languages');
    }
}
