<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "buy_out".
 *
 * @property int $buy_out_id
 * @property int $order_id 订单id
 * @property int $buy_out_price 买断百分比（50，买断50%）
 * @property int $buy_out_time 买断时间(24, 买断24小时)
 * @property string $add_time 生成时间
 */
class BuyOut extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'buy_out';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'buy_out_price', 'buy_out_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buy_out_id' => 'Buy Out ID',
            'order_id' => 'Order ID',
            'buy_out_price' => 'Buy Out Price',
            'buy_out_time' => 'Buy Out Time',
            'add_time' => 'Add Time',
        ];
    }

    /**
     * 增加买断信息
     * @param unknown $order_id
     */
    public function addBuyOutInfo(&$model){
        $modelUser = new User();
        $user_info = $modelUser->getMemberInfo(array('id' => $model->member_id));
        if($user_info['is_buy_out'] == 1){//判断用户是否买断
            $r1 = $this->addBuyOut(array('order_id'=>$model->order_id, 'buy_out_price'=>$user_info['buy_out_price'], 'buy_out_time'=>$user_info['buy_out_time'], 'add_time'=>time()));
            $r2 = $model->upOrder(array('state'=>1), array('order_id'=>$model->order_id));//更新用户二级状态为买断
            if(!$r1 || !$r2){
                return false;
            }
        }
        $model->collection_poundage_one = $user_info->is_poundage;
        return true;
    }

    /**
     * 增加
     */
    public function addBuyOut($upda) {
        $model = new BuyOut();
        $model->setAttributes($upda);
        return $model->save($upda);
    }

    public function deleteByOrderId($orderId) {
       return $this::deleteAll('order_id = :order_id', [':order_id' => $orderId]);
    }
}
