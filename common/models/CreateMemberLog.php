<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "create_member_log".
 *
 * @property int $id ID
 * @property string $logistics_sn 票号
 * @property string $member_phone 电话
 * @property int $old_member_id 更改前的member_id
 * @property int $new_member_id 更改后的member_id
 * @property int $old_order_state 更改前的order_state
 * @property int $new_order_state 更改后的order_state
 * @property int $old_state 更改前的state
 * @property int $new_state 更改后的state
 * @property int $old_goods_price_state 更改前的goods_price_state
 * @property int $new_goods_price_state 更改后的goods_price_state
 * @property int $user_id 操作人的id
 */
class CreateMemberLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'create_member_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logistics_sn', 'member_phone', 'old_member_id', 'new_member_id', 'old_order_state', 'new_order_state', 'old_state', 'new_state', 'old_goods_price_state', 'new_goods_price_state', 'user_id'], 'safe'],
            [['old_member_id', 'new_member_id', 'old_order_state', 'new_order_state', 'old_state', 'new_state', 'old_goods_price_state', 'new_goods_price_state', 'user_id'], 'integer'],
            [['logistics_sn', 'member_phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logistics_sn' => 'Logistics Sn',
            'member_phone' => 'Member Phone',
            'old_member_id' => 'Old Member ID',
            'new_member_id' => 'New Member ID',
            'old_order_state' => 'Old Order State',
            'new_order_state' => 'New Order State',
            'old_state' => 'Old State',
            'new_state' => 'New State',
            'old_goods_price_state' => 'Old Goods Price State',
            'new_goods_price_state' => 'New Goods Price State',
            'user_id' => 'User ID',
        ];
    }

    /*
     * 0.0
     * 增加 log 日记
     * */
    public function addCreateMemberLogInfo($data)/*,$orderState*/
    {

        $this->logistics_sn = $data->logistics_sn;
        $this->member_phone = $data->member_phone;
        $this->old_member_id = $data->getOldAttribute('member_id');
        $this->new_member_id = $data->member_id;
        $this->old_order_state = $data->getOldAttribute('order_state');
        $this->new_order_state = $data->order_state;/*$orderState*/
        $this->old_state = $data->getOldAttribute('state');
        $this->new_state = $data->state;
        $this->old_goods_price_state = $data->getOldAttribute('goods_price_state');
        $this->new_goods_price_state = $data->goods_price_state;
        $this->user_id = Yii::$app->user->id;
        return $this->insert();
    }
}
