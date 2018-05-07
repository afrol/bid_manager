<?php

use yii\db\Migration;

/**
 * Handles the creation of table `campaigns`.
 */
class m180410_075009_create_campaigns_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('campaigns', [
            'autoincrement_id' => $this->primaryKey()->unsigned(),
            'campaign_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0)->unique(),
            'api_token_id' => $this->integer(11)->notNull()->unsigned()->defaultValue(0),
            'name' => $this->string(255),
            'type_id' => $this->tinyInteger(4)->notNull()->unsigned()->defaultValue(0),
            'state_id' => $this->tinyInteger(4)->notNull()->unsigned()->defaultValue(0),
            'updated_at' => $this->timestamp()->notNull(),
        ], 'ENGINE=InnoDB CHARSET=utf8');

        $this->createIndex(
            'idx-campaigns-api_token_id',
            'campaigns',
            'api_token_id'
        );

        $this->createIndex(
            'idx-campaigns-state',
            'campaigns',
            'state_id'
        );

        $this->createIndex(
            'idx-campaigns-updated_at',
            'campaigns',
            'updated_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('campaigns');
    }
}
