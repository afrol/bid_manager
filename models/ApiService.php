<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_services".
 *
 * @property int $api_service_id
 * @property string $name
 */
class ApiService extends \yii\db\ActiveRecord
{

    const SERVICE_ID_YANDEX_DIRECT = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 125],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'api_service_id' => 'Api Service ID',
            'name' => 'Name',
        ];
    }
}
