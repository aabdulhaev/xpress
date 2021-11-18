<?php

use yii\db\Migration;

class m210916_034032_change_column_learn_time_of_material extends Migration
{
    public $tableName = '{{%materials}}';

    public function safeUp()
    {
        $this->dropColumn($this->tableName, 'learn_time');
        $this->addColumn($this->tableName, 'learn_time', $this->integer());
    }

    public function safeDown()
    {
        echo "m210916_034032_change_column_learn_time_of_material cannot be reverted.\n";

        return false;
    }
}
