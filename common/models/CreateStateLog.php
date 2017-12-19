<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "create_state_log".
 *
 * @property int $id ID
 * @property int $old_order_state 更改前的order_state
 * @property int $new_order_state 更改后的order_state
 * @property int $old_state 更改前的state
 * @property int $new_state 更改后的state
 * @property int $old_goods_price_state 更改前的goods_price_state
 * @property int $new_goods_price_state 更改后的goods_price_state
 * @property string $add_time 修改时间
 * @property int $user_id 操作人的id
 */
class CreateStateLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'create_state_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_order_state', 'new_order_state', 'old_state', 'new_state', 'old_goods_price_state', 'new_goods_price_state', 'add_time', 'user_id'], 'safe'],
            [['old_order_state', 'new_order_state', 'old_state', 'new_state', 'old_goods_price_state', 'new_goods_price_state', 'add_time', 'user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'old_order_state' => 'Old Order State',
            'new_order_state' => 'New Order State',
            'old_state' => 'Old State',
            'new_state' => 'New State',
            'old_goods_price_state' => 'Old Goods Price State',
            'new_goods_price_state' => 'New Goods Price State',
            'add_time' => 'Add Time',
            'user_id' => 'User ID',
        ];
    }

    /*
     * 0.0
     * 增加 log 日记
     * */

    public function addCreateStateLogInfo($data)
    {
        $this -> old_order_state = $data->getOldAttribute('order_state');
        $this -> new_order_state = $data -> order_state;
        $this -> old_state = $data -> getOldAttribute('state');
        $this -> new_state = $data -> state;
        $this -> old_goods_price_state = $data -> getOldAttribute('goods_price_state');
        $this -> new_goods_price_state = $data -> goods_price_state;
        $this->add_time = time();
        $this->user_id = Yii::$app->user->id;
    }
}
