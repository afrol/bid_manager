<?php

use yii\db\Migration;

/**
 * Handles the creation of table `bids`.
 */
class m180413_084725_create_bids_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('bids', [
            'autoincrement_id' => $this->primaryKey(),
            'ad_group_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'campaign_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'bid' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'context_bid' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'min_search_price' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'current_search_price' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'updated_at' => $this->timestamp()->notNull(),
        ], 'ENGINE=InnoDB CHARSET=utf8');

        $this->createIndex(
            'idx-bids-ad_groups-id_campaign_id',
            'bids',
            'ad_group_id, campaign_id',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('bids');
    }
}
