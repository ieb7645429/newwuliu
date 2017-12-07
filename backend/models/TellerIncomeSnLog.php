<?php

namespace backend\models;

use Yii;
use common\models\LogisticsOrder;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "teller_income_sn_log".
 *
 * @property int $id
 * @property int $number 编号
 * @property int $order_id 订单Id
 * @property string $order_sn 票号
 * @property string $rel_amount 实收
 * @property string $amount 金额
 * @property int $user_id 收款人Id
 * @property string $receiving 收款对象
 * @property string $add_time 收款时间
 */
class TellerIncomeSnLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teller_income_sn_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'order_id', 'order_sn', 'rel_amount', 'amount', 'user_id', 'receiving', 'add_time'], 'required'],
            [['number', 'order_id', 'user_id', 'add_time'], 'integer'],
            [['rel_amount', 'amount'], 'number'],
            [['order_sn'], 'string', 'max' => 255],
            [['receiving'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '编号',
            'order_id' => 'Order ID',
            'order_sn' => '票号',
            'count' => '票数',
            'amount' => '金额',
            'user_id' => '收款人',
            'receiving' => '交款人',
            'add_time' => '收款时间',
            'rel_amount' => '实收金额',
        ];
    }
    
    /**
     * 添加Log
     * @param unknown $params
     * @return boolean
     */
    public function addLog($params) {
        
        $this->number = $params['number'];
        $this->order_id = $params['order_id'];
        $this->order_sn = $params['order_sn'];
        $this->rel_amount = $params['rel_amount'];
        $this->amount = $params['amount'];
        $this->user_id = Yii::$app->user->id;
        $this->receiving = $params['receiving'];
        $this->add_time = time();
        return $this->save();
    }
    
    /**
     * 取得序号
     * @return number|mixed|boolean|string|NULL|\yii\db\false
     */
    public static function getNumber() {
        $number = self::find()->max('number');
        if ($number) {
            $number += 1;
        } else {
            $number = 1;
        }
        return $number;
    }
    
    public function getReceivingList() {
        return self::find()->select('receiving')->distinct('receiving')->asArray()->all();
    }
    
    /**
     * 用户名
     * @return \yii\db\ActiveQuery
     */
    public function getUserTrueName() {
        return $this->hasOne(AdminUser::className(), ['id' => 'user_id']);
    }
    
    public function getOrderState() {
        $order = LogisticsOrder::findOne($this->order_id);
        return $order->getOrderStateName($order->order_state, $order);
    }
    
    public function getGoodsPriceState() {
        $order = LogisticsOrder::findOne($this->order_id);
        return $order->getGoodsPriceStateName($order->goods_price_state);
    }
    
    public function getCollectionDisplay() {
        $order = LogisticsOrder::findOne($this->order_id);
        
        if($order->order_state < Yii::$app->params['returnOrderStateDriver']) {
            return false;
        }
        if($order->same_city == 1) {
            $model = new IncomeDriver();
        } else if ($order->same_city == 2) {
            $model = new IncomeTerminusNot();
        }
        return !$model->getIsConfirm(ArrayHelper::toArray($order));
    }
    
    public function getCollection2Display() {
        $order = LogisticsOrder::findOne($this->order_id);
        return $this->getCollectionDisplay() && $order->collection == 1;
    }
    
    public function getRemark() {
//         $remarks = OrderTellerRemark::findAll(['order_id' => $this->order_id]);
//         if($remarks) {
//             $return = '<table class="table table-bordered table-hover">';
//             foreach ($remarks as $remark) {
//                 $return .= '<tr>';
//                 $user = AdminUser::findOne($remark->user_id);
//                 $return .= '<td>'.$user->user_truename.'</td>';
//                 $return .= '<td>'.$remark->add_time.'</td>';
//                 $return .= '<td>'.$remark->content.'</td>';
//                 $return .= '</tr>';
//             }
//             $return .= '<td colspan="3">'.Html::button('修改备注', ['id'=>'remark_'.$this->order_id,'class' => 'btn btn-info remark_edit','data-id'=>$this->order_id,'data-toggle' => 'modal','data-target' => '#remark-modal']).'</td>';
//             $return .= '</table>';
//             return $return;
//         }
        return Html::button('查看修改备注', ['id'=>'remark_'.$this->order_id,'class' => 'btn btn-info remark_edit','data-id'=>$this->order_id,'data-toggle' => 'modal','data-target' => '#remark-modal']);
    }
    
    public function getRemarkExcel() {
        $remarks = OrderTellerRemark::findAll(['order_id' => $this->order_id]);
        if($remarks) {
            $return = array();
            foreach ($remarks as $remark) {
                $return[] = $remark->content;
            }
            return implode("\n", $return);
        }
        return '';
    }
}
