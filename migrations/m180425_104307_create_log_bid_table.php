<?php

use yii\db\Migration;

/**
 * Handles the creation of table `log_bid`.
 */
class m180425_104307_create_log_bid_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('log_bid', [
            'id' => $this->bigPrimaryKey(),
            'level' => $this->integer(),
            'category' => $this->string(),
            'log_time' => $this->double(),
            'prefix' => $this->string(),
            'message' => $this->text(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx_log_level', 'log_bid', 'level');
        $this->createIndex('idx_log_category', 'log_bid', 'category');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('log_bid');
    }
}
