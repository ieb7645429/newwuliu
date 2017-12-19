<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "api_token".
 *
 * @property int $id
 * @property string $token
 * @property string $company 公司
 * @property string $type 访问类型（暂时未用）
 */
class ApiToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['token', 'company', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'company' => 'Company',
            'type' => 'Type',
        ];
    }
}
