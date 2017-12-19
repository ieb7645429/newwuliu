<?php

namespace common\models;

use Yii;
/**
 * This is the model class for table "order_parts".
 *
 * @property int $order_id
 * @property string $parts_str 配件ID字符串
 * @property string $add_time 添加时间
 */
class OrderParts extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_parts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','member_id','buy_info_id'], 'required'],
            [['order_id'], 'integer'],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id','member_id', 'buy_info_id', 'p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7'
        ];
    }
    /**
     * 添加/修改记录
     * @param array $params
     * @return boolean
     */
    public function addOrderParts($params) {
        $model = $this::findOne($params['order_id']);
        if(empty($model)) {
            $model = $this;
        }
        $buyInfo = (new BuyInfo())->getBuyInfo($params['receiving_phone']);
        $model->order_id = intval($params['order_id']);
        $model->member_id = intval($params['member_id']);
        $model->buy_info_id = $buyInfo['id'];
        foreach ($params['parts'] as $key=>$val) {
            $model->{$key} = $val;
        }
        return $model->save();
    }

    /**
     * @desc 根据订单Id取得配件名
     * @param integer $id
     * @return string
     */
    public static function getPartsName($id) {
        $model = static::findOne($id);
        if($model) {
            $result = array();
            $columns = Parts::find()->select('parts_name')->indexBy('parts_id')->asArray()->column();
            foreach ($columns as $key=>$value) {
                $model->{'p'.$key} && $result[] = $value;
            }
            return $result ? implode(',',$result) : null;
        }
        return null;
    }
}
