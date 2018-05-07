<?php

namespace app\models;

use app\components\Token\TokenInterface;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "api_tokens".
 *
 * @property int $api_token_id
 * @property int $api_service_id
 * @property string $token
 * @property string $account_login
 * @property int $status_id
 * @property string $updated_at
 */
class ApiToken extends ActiveRecord implements TokenInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_service_id'], 'integer'],
            [['updated_at'], 'safe'],
            [['token', 'account_login'], 'string', 'max' => 255],
            [['status_id'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'api_token_id' => 'Api Token ID',
            'api_service_id' => 'Api Service ID',
            'token' => 'Token',
            'account_login' => 'Account Login',
            'status_id' => 'Status ID',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Relational rules
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(ApiService::class, ['api_service_id']);
    }

    public static function getTokens(array $tokensIds = [])
    {
        if (!$tokensIds) {
            return [];
        }

        return self::search($tokensIds)->getModels();
    }

    public static function search(array $tokensIds = [])
    {
        if (!$tokensIds) {
            return [];
        }

        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $query->andFilterWhere([
            'status_id' => self::STATUS_ENABLED,
            'api_service_id' => ApiService::SERVICE_ID_YANDEX_DIRECT,
            'api_token_id' => $tokensIds
        ]);

        return $dataProvider;
    }
}
