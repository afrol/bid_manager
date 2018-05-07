<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ad_groups`.
 */
class m180411_130457_create_ad_groups_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ad_groups', [
            'autoincrement_id' => $this->primaryKey(),
            'ad_group_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0)->unique(),
            'campaign_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'name' => $this->string(255),
            'type_id' => $this->tinyInteger(4)->notNull()->unsigned()->defaultValue(0),
            'status_id' => $this->tinyInteger(4)->notNull()->unsigned()->defaultValue(0),
            'updated_at' => $this->timestamp()->notNull(),
        ], 'ENGINE=InnoDB CHARSET=utf8');

        $this->createIndex(
            'idx-ad_groups-campaign_id',
            'ad_groups',
            'campaign_id'
        );

        $this->createIndex(
            'idx-ad_groups-status_id',
            'ad_groups',
            'status_id'
        );

        $this->createIndex(
            'idx-ad_groups-updated_at',
            'ad_groups',
            'updated_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('ad_groups');
    }
}
