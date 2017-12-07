<?php
namespace backend\modules\dl\models;

use Yii;
use yii\helpers\ArrayHelper;
use frontend\modules\dl\models\LogisticsReturnOrderSearch;
use frontend\modules\dl\models\LogisticsReturnOrder;
use frontend\modules\dl\models\ShippingTpye;
use frontend\modules\dl\models\Terminus;
use frontend\modules\dl\models\ReturnOrderTime;

class ReturnPayTerminus extends LogisticsReturnOrderSearch
{
    /**
     * 取得订单列表
     * @param unknown $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLogisticsOrderList($params) {
        $query = LogisticsReturnOrder::find();
        
        $this->load($params);

        $query->innerJoin('return_order_time', 'logistics_return_order.order_id = return_order_time.order_id')
              ->leftJoin('return_order_remark', 'logistics_return_order.order_id = return_order_remark.order_id');
        $query->select('logistics_return_order.*, return_order_time.price_time, return_order_time.income_freight_time, return_order_time.unload_time, return_order_remark.content');

        // 外阜
        $terminus_id= ArrayHelper::getValue($params, 'terminus_id', null);
        if($terminus_id) {
            $query->andFilterWhere(['logistics_return_order.terminus_id' => $terminus_id]);
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
        $query->andFilterWhere(['!=', 'logistics_return_order.terminus_id' , 0]);
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
     * @return array|number[]
     */
    public function formatData($datas) {
        if(empty($datas)) {
            return array();
        }
        $return = array();
        foreach ($datas as $data) {
            $key = $data['terminus_id'];
            $data['object_name']= Terminus::getNameById($key);
            $parameter= ['terminus_id' => $key];
            // 详细页链接
            $data['object_url_parameter'] = $this->_getObjectUrlParameter('teller/pay-terminus-details', $parameter);
            
            // 相同 落地点/同城 合并
            if (isset($return[$key])) {
                $return[$key]['freight'] += $data['freight'];
                $return[$key]['make_from_price'] += $data['make_from_price'];

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
            $data['object_name']= Terminus::getNameById($data['terminus_id']);
            
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
     * 是否已付
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
     * 计算总额
     * @param unknown $data
     * @return number[]|unknown[]
     */
    public function _getAmount($data) {
        $return = array(
            'all_amount' => 0,
            'finished_amount' => 0,
            'unfinished_amount' => 0,
        );

        $return['all_amount'] += $data['freight'] * $data['scale'] / 100;
        $return['all_amount'] += $data['make_from_price'];
        if($data['freight_state'] & 4) {
            $return['finished_amount'] = $return['all_amount'];
        } else {
            $return['unfinished_amount'] = $return['all_amount'];
        }

        return $return;
    }
    
    /**
     * 修改付运费，代收状态
     * @param int $orderId 订单id
     * @return boolean
     */
    public function setPayTerminus($orderId)
    {
        $data = $this::findOne($orderId);
        $modelReturnOrderTime = new ReturnOrderTime();
        $content = array();
        if($data->freight_state == 1 )
        {
            $data->freight_state = $data->freight_state|4;
            $res1 = $modelReturnOrderTime->orderTimeswitch('pay_freight_time', $data);
            if(!$res1)
            {
            	return false;
            }
            $content[] = TellerLog::CONTENT_FREIGHT;
        }
        if ($data->save()) {
            $tellerLog = new TellerLog();
            $params['order_id'] = $orderId;
            $params['order_type'] = 2;
            $params['content'] = implode(",", $content);
            $params['type'] = 2;
            $params['amount'] = ArrayHelper::getValue($this->_getAmount(ArrayHelper::toArray($data)), 'all_amount', 0);
            if($tellerLog->addLog($params)) {
                return [
                    'freight_state_name' => $this->getFreightStateName($data->freight_state),
                ];
            }
            return false;
        }
        return false;
    }
}