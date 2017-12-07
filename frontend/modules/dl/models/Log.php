<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int $order_id
 * @property int $old_order_state 更改前的order_state
 * @property int $new_order_state 更改后的order_state
 * @property int $old_state 更改前state
 * @property int $new_state 更改后state
 * @property int $user_id 操作人的id
 * @property string $add_time 时间
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
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
            [['order_id', 'old_order_state', 'new_order_state', 'old_state', 'new_state', 'user_id', 'add_time'], 'required'],
            [['order_id', 'old_order_state', 'new_order_state', 'old_state', 'new_state', 'user_id', 'add_time'], 'integer'],
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
            'old_order_state' => 'Old Order State',
            'new_order_state' => 'New Order State',
            'old_state' => 'Old State',
            'new_state' => 'New State',
            'user_id' => 'User ID',
            'add_time' => 'Add Time',
        ];
    }
    
    /**
     * 朱鹏飞
     * 增加log日记
     * @param unknown $data
     */
    public function addLogInfo($data, $orderState){
    	$this->order_id = $data->order_id;
    	$this->old_order_state = $data->getOldAttribute('order_state');
    	$this->new_order_state = $orderState;
    	$this->old_state = $data->getOldAttribute('state');
    	$this->new_state = $data->state;
    	$this->user_id = Yii::$app->user->id;
    	$this->add_time = time();
    	return $this->insert();
    }
}
