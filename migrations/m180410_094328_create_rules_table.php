<?php

use yii\db\Migration;

/**
 * Handles the creation of table `rules`.
 */
class m180410_094328_create_rules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('rules', [
            'rule_id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(100)->notNull()->defaultValue(''),
            'rule' => $this->text()->notNull()->defaultValue('')->comment('Serialized rule configuration'),
            'value' => $this->text()->notNull()->defaultValue('')->comment('Serialized value configuration'),
            'description' => $this->string(255)->notNull()->defaultValue(''),
            'status_id' => $this->tinyInteger(4)->unsigned()->notNull()->defaultValue(1),
            'position' => $this->tinyInteger(4)->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->timestamp()->notNull(),
        ], 'ENGINE=InnoDB CHARSET=utf8');

        $this->createIndex(
            'idx-rules-status_id',
            'rules',
            'status_id'
        );

        $this->createIndex(
            'idx-rules-position',
            'rules',
            'position'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('rules');
    }
}
