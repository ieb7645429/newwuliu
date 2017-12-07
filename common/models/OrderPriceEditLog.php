<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_price_edit_log".
 *
 * @property string $id
 * @property string $uid 用户id
 * @property string $amount 订单价钱
 * @property string $before_amount 前余额
 * @property string $after_amount 后余额
 * @property string $content 内容
 * @property int $type 类型（1收入，2支出）
 * @property string $order_sn 票号
 * @property string $add_time 添加时间
 * @property int $operation_member_id
 */
class OrderPriceEditLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_price_edit_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'content', 'type', 'order_sn', 'add_time'], 'required'],
            [['uid', 'type', 'add_time', 'operation_member_id'], 'integer'],
            [['amount', 'before_amount', 'after_amount'], 'number'],
            [['content'], 'string'],
            [['order_sn'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'amount' => 'Amount',
            'before_amount' => 'Before Amount',
            'after_amount' => 'After Amount',
            'content' => 'Content',
            'type' => 'Type',
            'order_sn' => 'Order Sn',
            'add_time' => 'Add Time',
            'operation_member_id' => 'Operation Member ID',
        ];
    }
    /** 
     * 封车后代收款修改log增加
     * @param unknown $params
     */
    public function addOrderPriceEditLog($params,$content = '修改'){
        if($params['model']->order_state<=10) return true;//未封车不处理余额
        $this->uid = $params['model']->member_id;
        $this->amount = abs($params['after_amount']-$params['before_amount']);
        $this->before_amount = $params['before_amount'];
        $this->after_amount = $params['after_amount'];
        $this->content = $content;
        $this->type = $params['after_amount']-$params['before_amount']>0?1:2;
        $this->order_sn = $params['model']->logistics_sn;
        $this->add_time = time();
        $this->operation_member_id = Yii::$app->user->id;
        return $this->save();
    }
}
