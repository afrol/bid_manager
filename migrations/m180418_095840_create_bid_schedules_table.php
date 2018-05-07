<?php

use yii\db\Migration;

/**
 * Handles the creation of table `bid_schedules`.
 */
class m180418_095840_create_bid_schedules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('bid_schedules', [
            'autoincrement_id' => $this->primaryKey(),
            'ad_group_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'rule_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'bid' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'bid_processed' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'status_id' => $this->tinyInteger(4)->notNull()->unsigned()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], 'ENGINE=InnoDB CHARSET=utf8');

        $this->createIndex(
            'idx-bid_schedules-ad_group_id',
            'bid_schedules',
            'ad_group_id'
        );

        $this->createIndex(
            'idx-bid_schedules-status_id',
            'bid_schedules',
            'status_id'
        );

        $this->createIndex(
            'idx-bid_schedules-created_at',
            'bid_schedules',
            'created_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('bid_schedules');
    }
}
