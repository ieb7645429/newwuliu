<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "terminus_user".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $terminus_id 落地点id
 */
class TerminusUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'terminus_user';
    }
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_dl');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'terminus_id'], 'required'],
            [['user_id', 'terminus_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'terminus_id' => 'Terminus ID',
        ];
    }
	/**
     * 根据userid获取落地点id
     * 小雨
     */
    public static function getById($id) {
        return self::findOne($id);
    }
}
