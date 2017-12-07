<?php
namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\LogisticsOrderSearch;
use common\models\LogisticsOrder;
use common\models\User;
use common\models\OrderTime;
use backend\models\TellerLog;

class IncomeEmployee extends LogisticsOrderSearch
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
        $query->select('logistics_order.*, order_time.price_time, order_time.unload_time');

        $employee_id= ArrayHelper::getValue($params, 'employee_id', null);
        if($employee_id) {
            $query->andFilterWhere(['employee_id' => $employee_id]);
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
        // 订单状态为开单以上
        $query->andFilterWhere(['>=', 'order_state' , Yii::$app->params['orderStateEmployee']]);
        // 运费为先付
        $query->andFilterWhere(['shipping_type' => 3]);
        // 开单时间
        $query->andFilterWhere(['between', 'price_time', $start, $end]);

        return $query->asArray()->all();
    }
    
    /**
     * 格式化数据
     * @param unknown $datas
     * @return array|array[]|number|unknown
     */
    public function formatData($datas) {
        if(empty($datas)) {
            return array();
        }
        $return = array();
        foreach ($datas as $data) {
            $key = $data['employee_id'];
            $data['object_name'] = User::findIdentity($key)->user_truename;
            $parameter = ['employee_id' => $key];
            $data['object_url_parameter'] = $this->_getObjectUrlParameter('teller/income-employee-details', $parameter);
            // 相同开单员合并
            if (isset($return[$key])) {
                $return[$key]['freight'] += $data['freight'];
                $return[$key]['make_from_price'] += $data['make_from_price'];
                $return[$key]['shipping_sale'] += $data['shipping_sale'];

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
     * @return array|number[]
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
            
            $data['object_name']= User::findIdentity($data['employee_id'])->user_truename;
            $data['freight_state_name'] = $this->getFreightStateName($data['freight_state']);
            $data['price_time'] = date('Y-m-d H:i:s', $data['price_time']);
            $data['unload_time'] = $data['unload_time']?date('Y-m-d H:i:s', $data['unload_time']):'';
            
            $data = array_merge($data, $this->_getAmount($data));
            $total['all_amount'] += $data['all_amount'];
            $total['finished_amount'] += $data['finished_amount'];
            $total['unfinished_amount'] += $data['unfinished_amount'];
            
            // 未收款且应收0 开单时间大于1天 自动变成收款
            if (!$data['is_confirm'] && $data['all_amount'] == 0 && (time() - strtotime($data['price_time'])) > 60*60*24) {
                $result = $this->setFreightState([$data['order_id']]);
                if($result != false) {
                    $data['freight_state_name'] = $result[0]['freight_state_name'];
                }
                $data['is_confirm'] = true;
            }
            
            $return[] = $data;
        }
        $return['total'] = $total;
        return $return;
    }
    
    /**
     * 是否已收
     * @param $data 订单信息
     */
    public function getIsConfirm($data){
        if($data['freight_state'] & 1){
            return true;
        }
        return false;
    }
    
    /**
     * 更新付运费状态与时间
     * @param int $orderId 订单id
     * @return true falses
     */
    public function setFreightState($orderIds) {
        $return = array();
        foreach ($orderIds as $orderId) {
            $data = self::findOne($orderId);
            if($data->order_state < 10)
            {
                return false;
            }
            if($data->abnormal != 2)
            {
                return false;
            }
            if($data->shipping_type != 3)
            {
                return false;
            }
            if($data->freight_state != 2)
            {
                continue;
            }
            $modelOrderTime = new OrderTime();
            $r1 = $this->updaFreightState($data, 1);
            $r2 = $modelOrderTime->orderTimeswitch('freight', $data);
            if($r1 && $r2)
            {
                $tellerLog = new TellerLog();
                $params['order_id'] = $orderId;
                $params['order_type'] = 1;
                $params['content'] = $tellerLog::CONTENT_FREIGHT;
                $params['type'] = 1;
                $params['amount'] = ArrayHelper::getValue($this->_getAmount(ArrayHelper::toArray($data)), 'all_amount', 0);
                if($tellerLog->addLog($params)) {
                    $temp = array(
                        'order_id' => $orderId,
                        'freight_state_name' => $this->getFreightStateName($data->freight_state)
                    );
                    $return[] = $temp;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return $return;
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
     * @return number[]|unknown[]
     */
    public function _getAmount($data) {
        $return = array(
            'all_amount' => 0,
            'finished_amount' => 0,
            'unfinished_amount' => 0,
        );
        
        $return['all_amount'] += $data['freight'];
        $return['all_amount'] += $data['make_from_price'];
        $return['all_amount'] -=  $data['shipping_sale'];
        // 代收时 收货款
        if ($data['freight_state'] & 1) {
            $return['finished_amount'] = $return['all_amount'];
        } else if($data['freight_state'] & 2) {
            $return['unfinished_amount'] = $return['all_amount'];
        }
        return $return;
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
        
        foreach ($logList as $log) {
            if (strstr($log['content'], TellerLog::CONTENT_FREIGHT) ) {
                return true;
            }
        }
        return false;
    }
}