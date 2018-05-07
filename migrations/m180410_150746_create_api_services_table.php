<?php

use yii\db\Migration;

/**
 * Handles the creation of table `api_services`.
 */
class m180410_150746_create_api_services_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('api_services', [
            'api_service_id' => $this->primaryKey(),
            'name' => $this->string(125)->notNull()->defaultValue(''),
        ]);

        $this->batchInsert(
            'api_services',
            ['name'],
            [
                ['Yandex Direct'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('api_services');
    }
}
