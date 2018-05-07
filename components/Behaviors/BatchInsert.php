<?php

namespace app\components\Behaviors;

use yii\base\Behavior;
use yii\db\Exception;

class BatchInsert extends Behavior
{

    /**
     * @param array $dataInsert
     * @param null $columns
     * @return int
     * @throws Exception
     * @throws \yii\base\NotSupportedException
     */
    public function insertUpdate(array $dataInsert, $columns = null)
    {
        if (!$dataInsert) {
            return 0;
        }

        /** @var \yii\db\Connection $db */
        $db = $this->owner->getDb();
        $onDuplicateKeyValues = [];

        if (!$columns) {
            $columns = $this->owner->attributes();
        }

        foreach ($columns as $itemColumn) {
            $column = $db->getSchema()->quoteColumnName($itemColumn);
            $onDuplicateKeyValues[] = $column . ' = VALUES(' . $column . ')';
        }

        $sql = $db->queryBuilder->batchInsert($this->owner->tableName(), $columns, $dataInsert);
        $sql .= ' ON DUPLICATE KEY UPDATE ' . implode(', ', $onDuplicateKeyValues);

        return $db->createCommand($sql)->execute();
    }

    /**
     * @param array $dataInsert
     * @param null $columns
     * @return int
     * @throws Exception
     */
    public function insertIgnore(array $dataInsert, $columns = null)
    {
        if (!$dataInsert) {
            return 0;
        }

        /** @var \yii\db\Connection $db */
        $db = $this->owner->getDb();

        if (!$columns) {
            $columns = $this->owner->attributes();
        }

        $sql = $db->queryBuilder->batchInsert($this->owner->tableName(), $columns, $dataInsert);
        $sql = str_replace('INSERT INTO', 'INSERT IGNORE', $sql);

        return $db->createCommand($sql)->execute();
    }

    /**
     * @param array $dataInsert
     * @param null $columns
     * @return int
     * @throws Exception execution failed
     */
    public function batchInsert(array $dataInsert, $columns = null)
    {
        if (!$dataInsert) {
            return 0;
        }

        if (!$columns) {
            $columns = $this->owner->attributes();
        }

        /** @var \yii\db\Connection $db */
        $db = $this->owner->getDb();

        return $db->createCommand()
            ->batchInsert($this->owner->tableName(), $columns, $dataInsert)
            ->execute();
    }
}
