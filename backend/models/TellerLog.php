<?php

namespace backend\models;

use Yii;
use common\models\LogisticsOrder;
use common\models\LogisticsReturnOrder;

/**
 * This is the model class for table "teller_log".
 *
 * @property int $id
 * @property int $order_id 订单Id
 * @property int $order_type 1发货，2退货
 * @property string $content 内容
 * @property int $type 类型(1收入，2支出)
 * @property string $amount 金额 
 * @property int $user_id 操作人Id
 * @property string $add_time 添加时间
 */
class TellerLog extends \yii\db\ActiveRecord
{
    const CONTENT_FREIGHT = 'freight';
    const CONTENT_GOODS_PRICE = 'goods_price';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teller_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'content', 'type', 'user_id'], 'required'],
            [['order_id', 'order_type', 'type', 'user_id'], 'integer'],
            [['add_time'], 'safe'],
            [['amount'], 'number'],
            [['content'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '票号',
            'order_type' => 'Order Type',
            'content' => '内容',
            'type' => '类型',
            'amount' => '金额',
            'user_id' => '用户',
            'add_time' => '时间',
        ];
    }

    /**
     * 添加财务操作Log
     * @param unknown $params
     */
    public function addLog($params) {
        $this->order_id = $params['order_id'];
        $this->order_type = $params['order_type'];
        $this->content = $params['content'];
        $this->type = $params['type'];
        $this->amount = $params['amount'];
        $this->user_id = Yii::$app->user->id;
        $this->add_time = date('Y-m-d H:i:s');
        return $this->save();
    }
    
    /**
     * 订单票号
     * @return string
     */
    public function getOrderSn() {
        if($this->order_type == 1) {
            return LogisticsOrder::findOne($this->order_id)->logistics_sn;
        } else if($this->order_type == 2) {
            return LogisticsReturnOrder::findOne($this->order_id)->logistics_sn;
        }
    }
    

    /**
     * 收款内容
     * @param unknown $content
     * @return string
     */
    public function getContent($content)
    {
        $arr = explode(',', $content);
        $return = array();
        foreach ($arr as $v) {
            if($v == self::CONTENT_FREIGHT) {
                $return[] = '运费';
            }else if($v == self::CONTENT_GOODS_PRICE) {
                $return[] = '代收款';
            }
        }
        return implode(",", $return);
    }
    
    /**
     * 用户名
     * @return \yii\db\ActiveQuery
     */
    public function getUserTrueName() {
        return $this->hasOne(AdminUser::className(), ['id' => 'user_id']);
    }

    /**
     * 取得记录
     * @param unknown $orderId
     * @return \yii\db\static[]
     */
    public static function getLogByCondition($condition) {
        return self::findAll($condition);
    }
}
