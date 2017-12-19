<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "teller_return_log".
 *
 * @property int $id
 * @property int $order_id 订单Id
 * @property int $type 类型(1收入，2支出)
 * @property int $user_id 操作人Id
 * @property string $add_time 添加时间
 */
class TellerReturnLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teller_return_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'type', 'user_id'], 'required'],
            [['order_id', 'type', 'user_id'], 'integer'],
            [['add_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'type' => 'Type',
            'user_id' => 'User ID',
            'add_time' => 'Add Time',
        ];
    }
    
    /**
     * 添加财务操作Log
     * @param unknown $params
     */
    public function addLog($params) {
        $this->order_id = $params['order_id'];
        $this->type = $params['type'];
        $this->user_id = Yii::$app->user->id;
        $this->add_time = date('Y-m-d H:i:s');
        return $this->save();
    }
}
