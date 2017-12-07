<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_third_advance".
 *
 * @property int $id
 * @property int $order_id
 * @property string $amount 垫付金额
 * @property string $logistics_sn 票号
 * @property int $state 状态（1已垫付，2已收款）
 * @property string $add_time 添加时间
 * @property int $add_user 添加用户Id
 * @property string $income_time 收款时间
 * @property int $income_user 收款用户Id
 */
class OrderThirdAdvance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_third_advance';
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
            'member_id' =>'member Id',
            'amount' => '订单金额',
            'logistics_sn' => '票号',
            'state' => 'State',
            'add_time' => 'Add Time',
            'add_user' => 'Add User',
            'income_time' => 'Income Time',
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
        $this->member_id = $params['member_id'];
        $this->amount = $params['amount'];
        $this->logistics_sn = $params['logistics_sn'];
        $this->add_time = time();
        $this->add_user = Yii::$app->user->id;
        return $this->save();
    }
    
    public function edit($order_id) {
        $data = self::findOne(['order_id' => $order_id]);
        if($data) {
            if($data->state == 1) {
                $data->state = 2;
                $data->income_time = time();
                $data->income_user = Yii::$app->user->id;
                return $data->save();
            }
        }
        return true;
    }
}
