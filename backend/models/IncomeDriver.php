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
use common\models\OrderThirdAdvance;
use common\models\UserBalance;

class IncomeDriver extends LogisticsOrderSearch
{
    /**
     * 取得订单列表
     * @param unknown $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLogisticsOrderList($params) {
        $query = LogisticsOrder::find();
        
//         $this->load($params);

        $query->innerJoin('order_time', 'logistics_order.order_id = order_time.order_id');
        $query->select('logistics_order.*, order_time.price_time, order_time.ruck_time');

        // 同城
        $driver_member_id= ArrayHelper::getValue($params, 'driver_member_id', null);
        if($driver_member_id) {
            $query->andFilterWhere(['driver_member_id' => $driver_member_id]);
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
        
        // 票号
        $logistics_sn = ArrayHelper::getValue($params, 'LogisticsOrderSearch.logistics_sn', null);
        if($logistics_sn){
            $query->andFilterWhere(['like', 'logistics_sn', $logistics_sn]);
        }

        // 不为测试数据
        $query->andFilterWhere(['test' => 2]);
        // 同城订单
        $query->andFilterWhere(['same_city' => 1]);
        // 订单状态为已完成
        $query->andFilterWhere(['=', 'order_state' , Yii::$app->params['orderStateDelivery']]);
        // 订单不能挂起
        $query->andFilterWhere(['abnormal' => 2]);

        $condition = [
            'and',
            // 装车时间
            ['between', 'ruck_time', $start, $end],
            // 收款状态为已收款
//             ['&', 'state', Yii::$app->params['orderReceived']],
            // 代收 或者 不代收并且运费为提收
            ['or', ['collection' => 1], ['and',['shipping_type' => 1],['!=', 'collection',  '1'],]]
        ];
        $query->andFilterWhere($condition);

        return $query->asArray()->all();
    }
    
    /**
     * 格式化数据
     * @param unknown $datas
     * @return array|number|array[]
     */
    public function formatData($datas) {
        if(empty($datas)) {
            return array();
        }
        $return = array();
        foreach ($datas as $data) {
            $key = $data['driver_member_id'];
            $user = User::findIdentity($key);
            $data['object_name']= $user->username;
            $data['object_truename']= $user->user_truename;
            $parameter= ['driver_member_id' => $key];
            
            // 详细页链接
            $data['object_url_parameter'] = $this->_getObjectUrlParameter('teller/income-driver-details', $parameter);
            
            // 相同 司机 合并
            if (isset($return[$key])) {
                // 代收 加货值
                if ($data['collection'] == 1) {
                    $return[$key]['goods_price'] += $data['goods_price'];
                    
                    // 返货 减返货货值
                    if ($data['return_logistics_sn'] != '') {
                        $return[$key]['return_goods_price'] += $this->_getReturnGoodPrice($data);
                    }
                }
                // 提付 加运费
                if ($data['shipping_type'] == 1) {
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
                $return[$key] = $data;
                // 不是提付 删除运费
                if($data['shipping_type'] != 1) {
                    $return[$key]['freight'] = 0;
                    $return[$key]['make_from_price'] = 0;
                    $return[$key]['shipping_sale'] = 0;
                }
                
                $return[$key]['return_goods_price'] = 0;
                // 不代收 删除货值
                if ($data['collection'] != 1) {
                    $return[$key]['goods_price'] = 0;
                } else if ($data['collection'] == 1 && $data['return_logistics_sn'] != '') {
                    // 代收 返货 减返货货值
                    $return[$key]['return_goods_price'] = $this->_getReturnGoodPrice($data);
                }
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
        $total = array(
            'all_amount' => 0,
            'finished_amount' => 0,
            'unfinished_amount' => 0,
        );
        foreach ($datas as $data) {
            // 是否已收
            $data['is_confirm'] = $this->getIsConfirm($data);
            
            $data = array_merge($data, $this->_getAmount($data));
            $total['all_amount'] += $data['all_amount'];
            $total['finished_amount'] += $data['finished_amount'];
            $total['unfinished_amount'] += $data['unfinished_amount'];
            
            // 未收款且应收0 开单时间大于1天 自动变成收款
            if (!$data['is_confirm'] && $data['all_amount'] == 0 && (time() - strtotime($data['price_time'])) > 60*60*24) {
                $result = $this->terminusConfirmCollection([$data['order_id']], 0);
                if($result != false) {
                    $data['freight_state_name'] = $result[0]['freight_state_name'];
                    $data['goods_price_state_name'] = $result[0]['goods_price_state_name'];
                }
                $data['is_confirm'] = true;
            }
            
            // 应收为0 不显示
            if ($data['all_amount'] == 0) {
                continue;
            }
            
            $state = ArrayHelper::getValue(Yii::$app->request->queryParams, 'LogisticsOrderSearch.goods_price_state', '0');
            if ($state == 1 && !$data['is_confirm']) {
                continue;
            } else if ($state == 2 && $data['is_confirm']) {
                continue;
            }
            
            // 收款员只能看见自己收的款
            if ($data['is_confirm']) {
                if(!$this->_checkPermission($data)) {
                    continue;
                }
            }
            
            // 同城司机名
            $user = User::findIdentity($data['driver_member_id']);
            $data['object_name']= $user->username;
            $data['object_truename']= $user->user_truename;

            $data['collection_name'] = $this->getCollectionName($data['collection']);
            $data['shipping_type_name'] = ShippingTpye::getShippingTypeNameById($data['shipping_type']);
            $data['freight_state_name'] = $this->getFreightStateName($data['freight_state']);
            $data['goods_price_state_name'] = $this->getGoodsPriceStateName($data['goods_price_state']);
            $data['price_time'] = date('Y-m-d H:i:s', $data['price_time']);
            $data['ruck_time'] = $data['ruck_time']?date('Y-m-d H:i:s', $data['ruck_time']):'';
            
            // 代收 返货 减返货货值
            $data['return_goods_price'] = 0;
            if ($data['collection'] == 1 && $data['return_logistics_sn'] != '') {
                $data['return_goods_price'] = $this->_getReturnGoodPrice($data);
            }
            
            if ($data['shipping_type'] == 3) {
                $data['freight'] = 0;
                $data['make_from_price'] = 0;
            }

            $return[] = $data;
        }
        
        $return['total'] = $total;
        return $return;
    }
    /**
     * 落地点收款判断是否
     * 靳健
     * @param $data 订单信息
     */
    public function getIsConfirm($data){
        if($data['collection'] == 1 ) {
            if($data['goods_price_state'] & 2) {
                return false;
            }
        }else if($data['collection'] == 2) {
            if($data['shipping_type'] == 1) {
                if($data['freight_state'] & 2) {
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 落地点收款状态修改
     * 靳健
     */
    public function terminusConfirmCollection($orderIds, $advance = 0) {
        $return = [];
        foreach ($orderIds as $orderId) {
            $data = self::findOne($orderId);
            $model = new OrderTime();
            if($data->collection == 1) {//代收修改货值状态与运费状态
                // 已收
                if(($data->goods_price_state & 8) || ($data->goods_price_state & 1)) {
                    continue;
                }
                
                // 修改订单状态
                if(!$this->setOrderState($data)) {
                    return false;
                }
                
                $data->goods_price_state = 8;
                if(!$model->orderTimeswitch('price',$data)){
                    return false;
                }
                $tellerLog = new TellerLog();
                $content = array();
                $content[] = $tellerLog::CONTENT_GOODS_PRICE;
                
                if ($data->shipping_type != 3) {
                    $data->freight_state = $data->freight_state==2?1:$data->freight_state;
                    $data->freight_state = $data->freight_state==6?5:$data->freight_state;
                    if(!$model->orderTimeswitch('freight',$data)){
                        return false;
                    }
                    $content[] = $tellerLog::CONTENT_FREIGHT;
                }
                
                $params['order_id'] = $data->order_id;
                $params['order_type'] = 1;
                $params['content'] = implode(",", $content);
                $params['type'] = 1;
                $params['amount'] = ArrayHelper::getValue($this->_getAmount(ArrayHelper::toArray($data)), 'all_amount', 0);
                if(!$tellerLog->addLog($params)) {
                    return false;
                }
                
                // 添加垫付记录
                if($advance) {
                    $orderAdvance = new OrderAdvance();
                    $advanceData = array();
                    $advanceData['order_id'] = $data->order_id;
                    $advanceData['amount'] = $params['amount'];
                    $advanceData['logistics_sn'] = $data->logistics_sn;
                    if(!$orderAdvance->addAdvance($advanceData)) {
                        return false;
                    }
                }
                
                // 三方垫付
                $thirdAdvance = new OrderThirdAdvance();
                $thirdAdvance->edit($orderId);
                
                if (date('Ymd', $data['add_time']) != date('Ymd')) {
                    $data->goods_price_state = $data->goods_price_state | 1;
                    $userBalance = new UserBalance();
                    if (!$userBalance->editUserWithdrawalAmountInfo($data->order_id)) {
                        return false;
                    }
                }
            } else {
                if($data->shipping_type == 1){//不代收,如果提付修改运费状态
                    // 已收 不处理
                    if ($data->freight_state & 1) {
                        continue;
                    }
                    
                    // 修改订单状态
                    if(!$this->setOrderState($data)) {
                        return false;
                    }
                    
                    if(!$model->orderTimeswitch('freight',$data)){
                        return false;
                    }
                    $data->freight_state = $data->freight_state==2?1:$data->freight_state;
                    $data->freight_state = $data->freight_state==6?5:$data->freight_state;
                    
                    $tellerLog = new TellerLog();
                    $params['order_id'] = $data->order_id;
                    $params['order_type'] = 1;
                    $params['content'] = $tellerLog::CONTENT_FREIGHT;
                    $params['type'] = 1;
                    $params['amount'] = ArrayHelper::getValue($this->_getAmount(ArrayHelper::toArray($data)), 'all_amount', 0);
                    if(!$tellerLog->addLog($params)) {
                        return false;
                    }
                }
            }
            if($data->save()) {
                $return[] = ['order_id'=>$orderId, 'freight_state_name'=>$this->getFreightStateName($data->freight_state),'goods_price_state_name'=>$this->getGoodsPriceStateName($data->goods_price_state)];
            }
        }
        return $return;
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
        // 代收时 收货款
        if ($data['collection'] == 1) {
            $return['all_amount'] += $data['goods_price'];
            
            // 代收 返货 减返货货值
            if ($data['return_logistics_sn'] != '') {
                $return['all_amount'] -= $this->_getReturnGoodPrice($data);
            }
            
            if($data['goods_price_state'] & 1) {
                $return['finished_amount'] = $return['all_amount'];
            } else if($data['goods_price_state'] & 2) {
                $return['unfinished_amount'] = $return['all_amount'];
            }
        }
        
        // 提付时 收运费
        if ($data['shipping_type'] == 1) {
            $return['all_amount'] += $data['freight'];
            $return['all_amount'] += $data['make_from_price'];
            $return['all_amount'] -=  $data['shipping_sale'];
            
            if ($data['freight_state'] & 1) {
                $return['finished_amount'] += $data['freight'];
                $return['finished_amount'] += $data['make_from_price'];
                $return['finished_amount'] -=  $data['shipping_sale'];
            } else if ($data['freight_state'] & 2) {
                $return['unfinished_amount'] += $data['freight'];
                $return['unfinished_amount'] += $data['make_from_price'];
                $return['unfinished_amount'] -=  $data['shipping_sale'];
            }
        }
        return $return;
    }

    /**
     * 取得返货 货值
     * @param unknown $returnSn
     * @return number
     */
    public function _getReturnGoodPrice($data) {
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
    
    /**
     * 判断是否显示
     * @param unknown $data
     * @return boolean
     */
    private function _checkPermission($data) {
        $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        // 财务看见所有记录
        if (!in_array(Yii::$app->params['roleTellerIncome'], $roles)) {
            return true;
        }
        // 收款员只能查看自己收款订单
        $condition = array();
        $condition['order_id'] = $data['order_id'];
        $condition['order_type'] = 1;
        $condition['type'] = 1;
        $condition['user_id'] = Yii::$app->user->id;
        $logList = TellerLog::getLogByCondition($condition);
        // 代收收货值
        if($data['collection'] == 1) {
            $content = TellerLog::CONTENT_GOODS_PRICE;
        } else if ($data['shipping_type'] != 3) {
            // 不是先付收运费
            $content = TellerLog::CONTENT_FREIGHT;
        } else {
            return false;
        }

        foreach ($logList as $log) {
            if (strstr($log['content'], $content) ) {
                return true;
            }
        }
        return false;
    }
}