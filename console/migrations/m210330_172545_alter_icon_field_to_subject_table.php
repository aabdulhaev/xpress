<?php

use yii\db\Migration;

/**
 * Class m210330_172545_alter_icon_field_to_subject_table
 */
class m210330_172545_alter_icon_field_to_subject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%subject}}', 'icon', 'img_name');
        $this->renameColumn('{{%competence}}', 'icon', 'img_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210330_172545_alter_icon_field_to_subject_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210330_172545_alter_icon_field_to_subject_table cannot be reverted.\n";

        return false;
    }
    */
}
