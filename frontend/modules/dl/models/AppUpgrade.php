<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "app_upgrade".
 *
 * @property int $app_upgrade_id
 * @property string $app_upgrade_version android版本号
 * @property int $app_upgrade_status 是否强制更新
 * @property string $app_upgrade_url app下载地址
 * @property string $app_upgrade_time 时间
 */
class AppUpgrade extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_upgrade';
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
            [['app_upgrade_version', 'app_upgrade_url'], 'required'],
            [['app_upgrade_version'], 'number'],
            [['app_upgrade_status'], 'integer'],
            [['app_upgrade_time'], 'safe'],
            [['app_upgrade_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'app_upgrade_id' => 'App Upgrade ID',
            'app_upgrade_version' => 'App Upgrade Version',
            'app_upgrade_status' => 'App Upgrade Status',
            'app_upgrade_url' => 'App Upgrade Url',
            'app_upgrade_time' => 'App Upgrade Time',
        ];
    }
	public function GetInfo($type){
	  return self::find()->select('app_upgrade_version,app_upgrade_url,app_upgrade_status')->where(['type'=>$type])->asArray()->one();
	}
}
