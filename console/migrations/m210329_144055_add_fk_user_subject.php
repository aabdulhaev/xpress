<?php

use yii\db\Migration;

/**
 * Class m210329_144055_add_fk_user_subject
 */
class m210329_144055_add_fk_user_subject extends Migration
{
    public $tableName = '{{%user_subject}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $userSubjectTable = Yii::$app->db->schema->getTableSchema('user_subject');
        if (!$userSubjectTable) {
            return false;
        }

        $this->addForeignKey(
            'fk_user_subject_user',
            $this->tableName,
            'user_uuid',
            '{{%user}}',
            'user_uuid',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_user_subject_subject',
            $this->tableName,
            'subject_uuid',
            '{{%subject}}',
            'subject_uuid',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_subject_user',$this->tableName);
        $this->dropForeignKey('fk_user_subject_subject',$this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210329_144055_add_fk_user_subject cannot be reverted.\n";

        return false;
    }
    */
}
