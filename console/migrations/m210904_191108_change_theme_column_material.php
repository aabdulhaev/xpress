<?php

use yii\db\Migration;

/**
 * Class m210904_191108_change_theme_column_material
 */
class m210904_191108_change_theme_column_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%materials}}', 'theme');
        $this->addColumn('{{%materials}}', 'theme_uuid', 'UUID');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%materials}}', 'theme_uuid');
        $this->addColumn('{{%materials}}', 'theme', $this->string(255));
    }
}
