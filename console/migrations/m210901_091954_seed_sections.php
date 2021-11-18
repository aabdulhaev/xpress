<?php

use common\access\Rbac;
use common\models\Section;
use common\models\User;
use yii\db\Migration;

/**
 * Class m210901_091954_seed_sections
 */
class m210901_091954_seed_sections extends Migration
{
    public $tableName = '{{%sections}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            $this->tableName,
            [
                'section_uuid' => Section::SECTION_LIBRARY_UUID,
                'title' => 'Библиотека знаний',
                'description' => 'Библиотека знаний',
                'created_by' => User::SEED_ADMIN_UUID,
                'created_at' => time(),
            ]
        );

        $this->insert(
            $this->tableName,
            [
                'section_uuid' => Section::SECTION_WEBINAR_UUID,
                'title' => 'Вебинары',
                'description' => 'Вебинары',
                'created_by' => User::SEED_ADMIN_UUID,
                'created_at' => time(),
            ]
        );

        $this->insert(
            $this->tableName,
            [
                'section_uuid' => Section::SECTION_POLL_UUID,
                'title' => 'Опросы',
                'description' => 'Опросы',
                'created_by' => User::SEED_ADMIN_UUID,
                'created_at' => time(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable($this->tableName);
    }
}
