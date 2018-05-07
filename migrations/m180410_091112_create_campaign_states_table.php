<?php

use yii\db\Migration;

/**
 * Handles the creation of table `campaign_states`.
 */
class m180410_091112_create_campaign_states_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('campaign_states', [
            'campaign_state_id' => $this->primaryKey()->unsigned(),
            'state_title' => $this->string(100),
        ], 'ENGINE=InnoDB CHARSET=utf8');


        $this->batchInsert(
            'campaign_states',
            ['state_title'],
            [
                ['UNKNOWN'],
                ['ARCHIVED'],
                ['CONVERTED'],
                ['ENDED'],
                ['OFF'],
                ['ON'],
                ['SUSPENDED'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('campaign_states');
    }
}
