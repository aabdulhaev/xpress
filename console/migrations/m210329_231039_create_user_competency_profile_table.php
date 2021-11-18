<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_competency_profile}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m210329_231039_create_user_competency_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_competency_profile}}', [
            'pk_uuid' => 'UUID NOT NULL UNIQUE',
            'user_uuid' => 'UUID NOT NULL ',
            'image' => $this->string(1048),
            'created_at' => $this->integer(),
            'PRIMARY KEY(pk_uuid)'
        ]);

        // creates index for column `user_uuid`
        $this->createIndex(
            '{{%idx-user_competency_profile-user_uuid}}',
            '{{%user_competency_profile}}',
            'user_uuid'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_competency_profile-user_uuid}}',
            '{{%user_competency_profile}}',
            'user_uuid',
            '{{%user}}',
            'user_uuid',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_competency_profile-user_uuid}}',
            '{{%user_competency_profile}}'
        );

        // drops index for column `user_uuid`
        $this->dropIndex(
            '{{%idx-user_competency_profile-user_uuid}}',
            '{{%user_competency_profile}}'
        );

        $this->dropTable('{{%user_competency_profile}}');
    }
}
