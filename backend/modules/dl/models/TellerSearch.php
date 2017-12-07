<?php

namespace backend\modules\dl\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use frontend\modules\dl\models\LogisticsOrderSearch;
use frontend\modules\dl\models\User;
use frontend\modules\dl\models\Terminus;
use backend\modules\dl\models\OrderRemark;

/**
 * TellerLogSearch represents the model behind the search form of `backend\models\TellerLog`.
 */
class TellerSearch extends LogisticsOrderSearch
{
    public function search($params, $type='')
    {
        $query = TellerSearch::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);
        
        $query->innerJoin('order_time', 'logistics_order.order_id = order_time.order_id');
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        
        // 查询时间
        $add_time = ArrayHelper::getValue($params, 'TellerSearch.add_time', null);
        if($add_time) {
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end.' 23:59:59');
        } else {
            $start = strtotime(date('Y-m-d'));
            $end = strtotime(date('Y-m-d').' 23:59:59');
        }
        $query->andFilterWhere(['between', 'price_time', $start, $end]);
        
        // 订单状态
        $state = ArrayHelper::getValue(Yii::$app->request->queryParams, 'TellerSearch.goods_price_state', '0');
        if($state == '1') {
            $query->andFilterWhere(['or', ['and', ['collection' => 2], ['&', 'freight_state', $state]], ['and', ['collection' => 1], ['&', 'goods_price_state', $state]]]);
        } else if ($state == '2') {
            $query->andFilterWhere(['or', ['and', ['collection' => 2], ['&', 'freight_state', $state], ['!=', '`freight` - `shipping_sale`', 0]], ['and', ['collection' => 1], ['&', 'goods_price_state', $state], ['or', ['!=', 'goods_price', 0], ['!=', '`freight` - `shipping_sale`', 0]]]]);
        }
        
        //司机
        if($this->driverTrueName) {
            $query->join('LEFT JOIN','user as drivername','drivername.id = logistics_order.driver_member_id');
            $query->andFilterWhere(['like','drivername.user_truename',$this->driverTrueName]);
        }
        
        // 代收
        if($this->collection) {
            $query->andFilterWhere(['collection' => $this->collection]);
        }
        
        //垫付
        if($this->advance) {
            $query->join('LEFT JOIN','order_advance as advance','advance.order_id = logistics_order.order_id');
            $query->andFilterWhere(['like','advance.state',$this->advance]);
        }
        
        if($this->order_state==1) {
            $query->andFilterWhere(['>=','order_state',50]);
        } else if($this->order_state==2) {
            $query->andFilterWhere(['<','order_state',50]);
        }

//         $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        // 财务收款看见同城记录
//         if (!in_array(Yii::$app->params['roleTeller'], $roles)) {
//             $terminus = new Terminus();
//             $terminusIds = $terminus->getFictitiousTerminusId();
//             if ($terminusIds) {
//                 $query->andFilterWhere(['or', ['same_city' => 1], ['in', 'terminus_id', $terminusIds]]);
//             } else {
//                 $query->andFilterWhere(['same_city' => 1]);
//             }
//         }
        
        $query->andFilterWhere(['like', 'logistics_order.logistics_sn', $this->logistics_sn]);
        return $dataProvider;
    }
    
    /**
     * 返货货值
     */
    public function getReturnGoodPrice() {
        if ($this->same_city == 1) {
            $model = new IncomeDriver();
            return $model->_getReturnGoodPrice(ArrayHelper::toArray($this));
        } else if ($this->same_city == 2) {
            $model = new IncomeTerminus();
            return $model->_getReturnGoodPrice(ArrayHelper::toArray($this));
        }
    }
    
    /**
     * 运费收款对象
     * @return unknown|string
     */
    public function getFreightMember() {
        if ($this->shipping_type == 3) {
            return User::findIdentity($this->employee_id)->user_truename;
        }
        return '';
    }
    
    /**
     * 代收款收款对象
     * @return string|unknown
     */
    public function getGoodsPriceMember() {
        if($this->collection == 2 && $this->shipping_type != 1) {
            return '';
        } else {
            if($this->same_city == 1) {
                $user = User::findIdentity($this->driver_member_id);
                if($user){
                    return $user->user_truename;
                }
                return '';
            } else if ($this->same_city == 2) {
                return Terminus::getNameById($this->terminus_id);
            }
        }
    }
    
    /**
     * 已付运费
     * @return \backend\models\number|\backend\models\unknown|number
     */
    public function getFreightValue() {
        if ($this->shipping_type == 3) {
            $model = new IncomeEmployee();
            return $model->_getAmount(ArrayHelper::toArray($this))['all_amount'];
        } else {
            return 0;
        }
    }
    
    /**
     * 提付运费+代收款
     * @return \backend\models\number|\backend\models\unknown
     */
    public function getGoodsPriceValue() {
        if($this->same_city == 1) {
            $model = new IncomeDriver();
        } else if ($this->same_city == 2) {
            $model = new IncomeTerminus();
        }
        return $model->_getAmount(ArrayHelper::toArray($this))['all_amount'];
    }
    
    /**
     * 已付运费收款地址
     * @return string
     */
    public function getFreightUrl() {
        return '?r=dl/teller/income-employee-confirm';
    }
    
    /**
     * 代收款收款地址
     * @return string
     */
    public function getGoodsFreightUrl() {
        if($this->same_city == 1) {
            return '?r=dl/teller/income-driver-confirm';
        } else if ($this->same_city == 2) {
            return '?r=dl/teller/income-terminus-confirm';
        }
    }
    
    /**
     * 已付运费按钮显示条件
     * @return boolean
     */
    public function getFreightDisplay() {
        if ($this->shipping_type == 3) {
            $model = new IncomeEmployee();
            return !$model->getIsConfirm(ArrayHelper::toArray($this));
        }
        return false;
    }
    
    /**
     * 代收款按钮显示条件
     * @return boolean
     */
    public function getGoodsDisplay() {
        if($this->order_state < Yii::$app->params['returnOrderStateDriver']) {
            return false;
        }
        
        if ($this->shipping_type == 3) {
            if($this->same_city == 1) {
                $model = new IncomeDriver();
            } else if ($this->same_city == 2) {
                $model = new IncomeTerminus();
            }
            return !$model->getIsConfirm(ArrayHelper::toArray($this));
        }
        return false;
    }
    
    /**
     * 提付运费+代收款显示条件
     * @return boolean
     */
    public function getFreightGoodsDisplay() {
        if($this->order_state < Yii::$app->params['returnOrderStateDriver']) {
            return false;
        }
        
        if ($this->shipping_type != 3) {
            if($this->same_city == 1) {
                $model = new IncomeDriver();
            } else if ($this->same_city == 2) {
                $model = new IncomeTerminus();
            }
            return !$model->getIsConfirm(ArrayHelper::toArray($this));
        }
        return false;
    }
    
    public function getRemark() {
       $remark = OrderRemark::findOne($this->order_id);
       if($remark) {
           return $remark->content;
       }
       return '';
    }
}
