<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "date_num".
 *
 * @property string $date
 * @property int $num 计数
 */
class DateNum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'date_num';
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
            [['date'], 'required'],
            [['date'], 'safe'],
            [['num'], 'integer'],
            [['date'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'num' => 'Num',
        ];
    }
    
    /**
     * @param int $time 时间戳
     */
    public function dateNumInfo($time)
    {
        $today = date('Y-m-d', $time);
        $dataInfo = self::findOne($today);
        if(empty($dataInfo))
        {
            $this->addDateNum($today);
            return 1;
        }else{
            $dataInfo->num = $dataInfo->getOldAttribute('num')+1;
            $dataInfo->save();
            return $dataInfo->num;
        }
    }
    
    public function addDateNum($today)
    {
        $this->date = $today;
        $this->save();
    }
}
