<?php
namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\LogisticsOrderSearch;
use common\models\LogisticsOrder;
use common\models\ShippingTpye;
use common\models\OrderTime;

class PayTerminus extends LogisticsOrderSearch
{
    /**
     * 取得订单列表
     * @param unknown $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLogisticsOrderList($params) {
        $query = LogisticsOrder::find();
        
        $this->load($params);

        $query->innerJoin('order_time', 'logistics_order.order_id = order_time.order_id');
        $query->select('logistics_order.*, order_time.price_time, order_time.income_freight_time, order_time.unload_time');

        $terminus_id= ArrayHelper::getValue($params, 'terminus_id', null);
        if($terminus_id) {
            $query->andFilterWhere(['logistics_order.terminus_id' => $terminus_id]);
        }

        // 查询时间
        $add_time = ArrayHelper::getValue($params, 'LogisticsOrderSearch.add_time', null);
        if($add_time) {
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end.' 23:59:59');
        } else {
            $start = strtotime(date('Y-m-d'));
            $end = strtotime(date('Y-m-d').' 23:59:59');
        }

        // 不为测试数据
        $query->andFilterWhere(['test' => 2]);
        // 订单状态为已开单 以上
        $query->andFilterWhere(['>=', 'order_state' , Yii::$app->params['orderStateEmployee']]);
        // 不是同城订单
        $query->andFilterWhere(['!=', 'logistics_order.terminus_id' , 0]);
        // 订单不能挂起
        $query->andFilterWhere(['abnormal' => 2]);
        // 运费状态为已收
        $query->andFilterWhere(['&', 'freight_state', 1]);
        // 财务收款时间
        $query->andFilterWhere(['between','income_freight_time',$start,$end]);

        return $query->asArray()->all();
    }
    
    /**
     * 格式化数据
     * @param unknown $datas
     * @return array|array[]|number
     */
    public function formatData($datas) {
        if(empty($datas)) {
            return array();
        }
        $return = array();
        foreach ($datas as $data) {
            $key = $data['terminus_id'];
            $parameter = ['terminus_id' => $key];
            $data['object_url_parameter'] = $this->_getObjectUrlParameter('teller/pay-terminus-details', $parameter);
            
            // 相同 落地点 合并
            if (isset($return[$key])) {
                $return[$key]['freight'] += $data['freight'];
                
                // 计算总额
                $amount = $this->_getAmount($data);
                $return[$key]['all_amount'] += $amount['all_amount'];
                $return[$key]['finished_amount'] += $amount['finished_amount'];
                $return[$key]['unfinished_amount'] += $amount['unfinished_amount'];
            } else {
                $data = array_merge($data, $this->_getAmount($data));
                $return[$key] = $data;
            }
        }
        return $return;
    }
    
    /**
     * 格式化详细页数据
     * @param unknown $datas
     * @return array|boolean[]
     */
    public function formatDetailsData($datas) {
        if(empty($datas)) {
            return array();
        }
        $return = array();
        $total= array(
            'all_amount' => 0,
            'finished_amount' => 0,
            'unfinished_amount' => 0,
        );
        foreach ($datas as $data) {
            $data = array_merge($data, $this->_getAmount($data));
            $total['all_amount'] += $data['all_amount'];
            $total['finished_amount'] += $data['finished_amount'];
            $total['unfinished_amount'] += $data['unfinished_amount'];
            
            $data['shipping_type_name'] = ShippingTpye::getShippingTypeNameById($data['shipping_type']);
            $data['freight_state_name'] = $this->getFreightStateName($data['freight_state']);
            $data['price_time'] = date('Y-m-d H:i:s', $data['price_time']);
            $data['unload_time'] = $data['unload_time']?date('Y-m-d H:i:s', $data['unload_time']):'';
            $data['income_freight_time'] = $data['income_freight_time']?date('Y-m-d H:i:s', $data['income_freight_time']):'';
            
            // 是否已付
            $data['is_confirm'] = $this->getIsConfirm($data);
            $return[] = $data;
        }
        $return['total'] = $total;
        return $return;
    }
    
    /**
     * 是否已付运费给落地点
     * @param unknown $data
     * @return boolean
     */
    public function getIsConfirm($data) {
        if($data['freight_state'] & 4) {
            return true;
        }
        return false;
    }
    
    /**
     * 更新付运费状态与时间
     * @param int $orderId 订单id
     * @return true falses
     */
    public function setFreightState($orderId){
        $data = self::findOne($orderId);
        if($data->order_state < 10)
        {
            return false;
        }
        if($data->abnormal != 2)
        {
            return false;
        }
        if($data->freight_state != 1)
        {
            return false;
        }
        $modelOrderTime = new OrderTime();
        $r1 = $this->updaFreightState($data, $data->freight_state|4);
        $r2 = $modelOrderTime->orderTimeswitch('pay_freight_time', $data);
        if($r1 && $r2)
        {
            $tellerLog = new TellerLog();
            $params['order_id'] = $orderId;
            $params['order_type'] = 1;
            $params['content'] = $tellerLog::CONTENT_FREIGHT;
            $params['type'] = 2;
            $params['amount'] = ArrayHelper::getValue($this->_getAmount(ArrayHelper::toArray($data)), 'all_amount', 0);
            if($tellerLog->addLog($params)) {
                return $this->getFreightStateName($data->freight_state);
            }
            return false;
        }
        return false;
    }
    
    /**
     * 更新运费状态
     * @param object $data 订单对像
     * @param int $state 运费状态
     * @return true false
     */
    public function updaFreightState($data, $state)
    {
        $data->freight_state = $state;
        return $data->save();
    }
    
    /**
     * 计算总额
     * @param unknown $data
     * @return number[]
     */
    public function _getAmount($data) {
        $return = array(
            'all_amount' => 0,
            'finished_amount' => 0,
            'unfinished_amount' => 0,
        );
        $return['all_amount'] = $data['freight'] * $data['scale']/ 100;
        if($data['freight_state'] & 4) {
            $return['finished_amount'] = $return['all_amount'];
        } else {
            $return['unfinished_amount'] = $return['all_amount'];
        }
        return $return;
    }
}