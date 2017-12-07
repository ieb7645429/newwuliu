<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_teller_remark".
 *
 * @property int $id
 * @property int $order_id 订单Id
 * @property string $content 注释内容
 * @property int $user_id 用户ID
 * @property string $add_time
 */
class ReturnOrderTellerRemark extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_order_teller_remark';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id'], 'required'],
            [['order_id', 'user_id'], 'integer'],
            [['add_time'], 'safe'],
            [['content'], 'string', 'max' => 255],
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
            'content' => 'Content',
            'user_id' => 'User ID',
            'add_time' => 'Add Time',
        ];
    }
    
    public function addRemark($params) {
        $this->order_id = intval($params['order_id']);
        $this->content = $params['content'];
        $this->user_id = Yii::$app->user->id;
        $this->add_time = date("Y-m-d H:i:s");
        return $this->save();
    }
}
