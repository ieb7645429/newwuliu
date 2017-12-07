<?php
namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\LogisticsOrderSearch;
use common\models\LogisticsOrder;
use common\models\LogisticsReturnOrder;
use common\models\ShippingTpye;
use common\models\OrderTime;
use common\models\User;
use backend\models\TellerLog;

class PayDealer extends LogisticsOrderSearch
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

        $member_id = ArrayHelper::getValue($params, 'member_id', null);
        if($member_id) {
            $query->andFilterWhere(['member_id' => $member_id]);
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
        
        // 订单必须为代收
        $query->andFilterWhere(['collection' => 1]);
        // 订单状态为已开单 以上
        $query->andFilterWhere(['>=', 'order_state' , Yii::$app->params['orderStateEmployee']]);
        // 订单不能挂起
        $query->andFilterWhere(['abnormal' => 2]);

        $condition = [
            'or',
            [
                'and',
                // 开单时间
                ['between', 'price_time', $start, $end],
                // 买断订单
                ['&', 'state', Yii::$app->params['orderBuyOut']]
            ],
            [
                'and',
                // 财务收到货款时间
                ['between','income_price_time',$start,$end],
                // 不买断 落地点已收款
                ['&','state',(Yii::$app->params['orderNotBuyOut'] | Yii::$app->params['orderReceived'])],
                // 财务已收款
                ['&', 'goods_price_state', 1],
            ]
        ];
        $query->andFilterWhere($condition);

        return $query->asArray()->all();
    }
    
    /**
     * 格式化数据
     * @param unknown $datas
     * @return array|number[]|array[]|number|unknown
     */
    public function formatData($datas) {
        if(empty($datas)) {
            return array();
        }
        
        $return = array();
        foreach ($datas as $data) {
            $key = $data['member_id'];
            $parameter = ['member_id' => $key];
            // 详细页链接
            $data['object_url_parameter'] = $this->_getObjectUrlParameter('teller/pay-dealer-details', $parameter);
            // 相同 经销商 合并
            if (isset($return[$key])) {
                // 买断 收会员费
                $return[$key]['collection_poundage_one'] += $data['collection_poundage_one'];
                
                // 收代收手续费
                $return[$key]['collection_poundage_two'] += $data['collection_poundage_two'];
                // 货值
                $return[$key]['goods_price'] += $data['goods_price'];
                // 买断返货货值
                if($data['state'] & 2) {
                    $return[$key]['return_goods_price'] += $this->_getReturnGoodPrice($data);
                }
                
                // 友件订单记录佣金
                $data['goods_price_scale'] += $this->_getGoodPriceScale($data);
                
                // 回付时 收运费
                if ($data['shipping_type'] == 2) {
                    $return[$key]['freight'] += $data['freight'];
                    $return[$key]['make_from_price'] += $data['make_from_price'];
                    $return[$key]['shipping_sale'] += $data['shipping_sale'];
                }
                
                // 计算总额
                $amount = $this->_getAmount($data);
                $return[$key]['all_amount'] += $amount['all_amount'];
                $return[$key]['finished_amount'] += $amount['finished_amount'];
                $return[$key]['unfinished_amount'] += $amount['unfinished_amount'];
            } else {
                $data = array_merge($data, $this->_getAmount($data));
                
                // 邮件订单记录佣金
                $data['goods_price_scale'] = $this->_getGoodPriceScale($data);
                
                // 买断 计算返货货值
                $data['return_goods_price'] = 0;
                if($data['state'] & 2) {
                    $data['return_goods_price'] = $this->_getReturnGoodPrice($data);
                }
                
                // 不是回付时 不减运费
                if ($data['shipping_type'] != 2) {
                    $data['freight'] = 0;
                    $data['make_from_price'] = 0;
                    $data['shipping_sale'] = 0;
                }
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
        foreach ($datas as $data) {
            $data = array_merge($data, $this->_getAmount($data));
            $data['shipping_type_name'] = ShippingTpye::getShippingTypeNameById($data['shipping_type']);
            $data['freight_state_name'] = $this->getFreightStateName($data['freight_state']);
            $data['goods_price_state_name'] = $this->getGoodsPriceStateName($data['goods_price_state']);
            $data['state_name'] = $this->getStateName($data['state']);
            // 返货货值
            $data['return_goods_price'] = $this->_getReturnGoodPrice($data);
            // 友件
            $data['goods_price_scale'] = 0;
            $data['goods_price_scale'] = $this->_getGoodPriceScale($data);
            
            // 是否已付
            $data['is_confirm'] = $this->getIsConfirm($data);
            $return[] = $data;
        }
        return $return;
    }
    
    /**
     * 是否已付
     * @param unknown $data
     * @return boolean
     */
    public function getIsConfirm($data) {
        if($data['goods_price_state'] & 4) {
            return true;
        }
        return false;
    }
    
    /**
     * 更新付给经销商货款状态与时间
     * @param int $orderId 订单id
     * @return true false
     */
    public function setGoodsPriceState($orderId)
    {
        $data = $this::findOne($orderId);
        if($data->order_state < 10)
        {
            return false;
        }
        if($data->abnormal != 2)
        {
            return false;
        }
        if($data->goods_price_state > 2)
        {
            return false;
        }
        $modelOrderTime = new OrderTime();
        $r1 = $this->upGoodsPriceState($data, $data->goods_price_state|4);
        $r2 = $modelOrderTime->orderTimeswitch('pay_price_time', $data);
        if($r1 && $r2)
        {
            $tellerLog = new TellerLog();
            $params['order_id'] = $orderId;
            $params['order_type'] = 1;
            $params['content'] = $tellerLog::CONTENT_GOODS_PRICE;
            $params['type'] = 2;
            $params['amount'] = ArrayHelper::getValue($this->_getAmount(ArrayHelper::toArray($data)), 'all_amount', 0);
            if($tellerLog->addLog($params)) {
                return $this->getGoodsPriceStateName($data->goods_price_state);
            }
            return false;
        }
        return false;
    }
    /**
     * 更新付给经销商货款状态
     * @param Object $data 订单对像
     * @param int $state 更新状态
     * @return true false
     */
    public function upGoodsPriceState($data, $state)
    {
        $data->goods_price_state = $state;
        return $data->save();
    }
    
    /**
     * 计算总额
     * @param unknown $data
     * @return number[]|unknown[]
     */
    public function _getAmount($data, $type = 0) {
        $return = array(
            'all_amount' => 0,
            'finished_amount' => 0,
            'unfinished_amount' => 0,
        );
        
        // 不代收
        if($data['collection'] == 2) {
            return $return;
        }

        // 货款
        if($data['state'] & 1) {
            $return['all_amount'] = $data['goods_price'];
        } else if($data['state'] & 2) {
            $return['all_amount'] = $data['goods_price'] - $this->_getReturnGoodPrice($data);
        }
        
        // 不计算返货
        if($type == 1) {
            $return['all_amount'] = $data['goods_price'];
        }

        // 友件订单扣除佣金 
        $params = ['goods_price' => $return['all_amount'], 'order_sn' => $data['order_sn'], 'member_id' => $data['member_id']];
        $return['all_amount'] -= $this->_getGoodPriceScale($params);
        
        // 全返 不减代收费
        if ($return['all_amount'] != 0) {
            // 减会员费
            $return['all_amount'] -= $data['collection_poundage_one'];
            
            // 减代收手续费
            $return['all_amount'] -= $data['collection_poundage_two'];
        }
        
        // 回付时 减运费
        if ($data['shipping_type'] == 2) {
            $return['all_amount'] -= $data['freight'];
            $return['all_amount'] -= $data['make_from_price'];
            $return['all_amount'] += $data['shipping_sale'];
        }
        if ($data['goods_price_state'] & 4) {
            $return['finished_amount'] = $return['all_amount'];
        } else {
            $return['unfinished_amount'] = $return['all_amount'];
        }
        return $return;
    }
    
    /**
     * 取得返货 货值
     * @param unknown $returnSn
     * @return number
     */
    private function _getReturnGoodPrice($data) {
        // 返货单号不为空
        if ($data['return_logistics_sn'] == '') {
            return 0;
        }
        
        $returnModel = new LogisticsReturnOrder();
        $returnInfo = $returnModel -> getOrderByLogisticsSn($data['return_logistics_sn']);

        if(empty($returnInfo)) {
            return 0;
        }
        // 退货
        if($returnInfo['return_type'] == 2) {
            return 0;
        }
        return $returnInfo['goods_price'];
    }
    
    private function _getGoodPriceScale($data) {
        $userInfo = User::findIdentity($data['member_id']);
        
        if($data['order_sn'] == '') {
            return 0;
        }
        $scale = $data['goods_price'] * $userInfo->store_commis / 100;
        if ($scale > 300) {
            $scale = 300;
        }
        return $scale;
    }
}