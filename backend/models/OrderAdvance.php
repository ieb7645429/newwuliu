<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_advance".
 *
 * @property int $id
 * @property int $order_id
 * @property string $amount 垫付金额
 * @property string $logistics_sn 票号
 * @property int $state 状态（1已收款，2未收款）
 * @property string $add_time 添加时间
 * @property int $add_user 添加用户Id
 * @property string $income_time 收款时间
 * @property int $income_user 收款用户Id
 */
class OrderAdvance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_advance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'logistics_sn', 'add_time', 'add_user'], 'required'],
            [['order_id', 'state', 'add_time', 'add_user', 'income_time', 'income_user'], 'integer'],
            [['amount'], 'number'],
            [['logistics_sn'], 'string', 'max' => 255],
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
            'amount' => '垫付金额',
            'logistics_sn' => '票号',
            'state' => '状态',
            'add_time' => '垫付时间',
            'add_user' => 'Add User',
            'income_time' => '收款时间',
            'income_user' => 'Income User',
        ];
    }
    
    /**
     * 添加垫付记录
     * @param unknown $params
     * @return boolean
     */
    public function addAdvance($params) {
        $this->order_id = $params['order_id'];
        $this->amount = $params['amount'];
        $this->logistics_sn = $params['logistics_sn'];
        $this->add_time = time();
        $this->add_user = Yii::$app->user->id;
        return $this->save();
    }
    
    public function getStateName() {
        if($this->state == 1) {
            return '已收款';
        } else if ($this->state == 2) {
            return '未收款';
        }
    }
    
    public function edit($id){
        $data = self::findOne($id);
        if($data->state !=2){
            return false;
        }
        $data->state = 1;
        $data->income_time = time();
        $data->income_user = Yii::$app->user->id;
        return $data->save();
    }
    
    public function getByOrderId($orderId) {
        return self::findOne(['order_id'=>$orderId]);
    }
}
