<?php

use yii\db\Migration;

/**
 * Handles the creation of table `campaign_types`.
 */
class m180410_124120_create_campaign_types_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('campaign_types', [
            'campaign_type_id' => $this->primaryKey()->unsigned(),
            'type_title' => $this->string(100),
        ], 'ENGINE=InnoDB CHARSET=utf8');

        $this->batchInsert(
            'campaign_types',
            ['type_title'],
            [
                ['UNKNOWN'],
                ['TEXT_CAMPAIGN'],
                ['MOBILE_APP_CAMPAIGN'],
                ['DYNAMIC_TEXT_CAMPAIGN']
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('campaign_types');
    }
}
