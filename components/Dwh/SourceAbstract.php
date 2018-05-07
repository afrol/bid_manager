<?php
namespace app\components\Dwh;

use Yii;
use yii\db\ActiveRecord;

abstract class SourceAbstract extends ActiveRecord
{

    public static function getDb()
    {
        return Yii::$app->mssql_dwh;
    }
}
