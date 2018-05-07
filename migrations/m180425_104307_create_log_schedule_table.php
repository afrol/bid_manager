<?php

use yii\db\Migration;

/**
 * Handles the creation of table `log_schedule`.
 */
class m180425_104307_create_log_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('log_schedule', [
            'id' => $this->bigPrimaryKey(),
            'level' => $this->integer(),
            'category' => $this->string(),
            'log_time' => $this->double(),
            'prefix' => $this->string(),
            'message' => $this->text(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx_log_level', 'log_schedule', 'level');
        $this->createIndex('idx_log_category', 'log_schedule', 'category');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('log_schedule');
    }
}
