<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_tokens`.
 */
class m180410_123248_create_api_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('api_tokens', [
            'api_token_id' => $this->primaryKey()->unsigned(),
            'api_service_id' => $this->integer(10)->notNull()->defaultValue(0)->unsigned(),
            'token' => $this->string(255)->notNull()->defaultValue(''),
            'account_login' => $this->string(255)->notNull()->defaultValue(''),
            'status_id' => $this->tinyInteger(4)->notNull()->defaultValue(1)->unsigned(),
            'updated_at' => $this->timestamp()->notNull(),
        ], 'ENGINE=InnoDB CHARSET=utf8');

        $this->createIndex(
            'idx-api_tokens-status_id',
            'api_tokens',
            'status_id'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('api_tokens');
    }
}
