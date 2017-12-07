<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use common\models\LogisticsOrderSearch;
use common\models\OrderThirdAdvance;
use yii\helpers\Html;

/**
 * TellerLogSearch represents the model behind the search form of `backend\models\TellerLog`.
 */
class TellerThirdAdvance extends LogisticsOrderSearch
{
    public function search($params, $type='')
    {
        $query = TellerThirdAdvance::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);
        
        $query->innerJoin('order_time', 'logistics_order.order_id = order_time.order_id');
        
        // 不为测试数据
        $query->andFilterWhere(['test' => 2]);
        // 订单不能挂起
        $query->andFilterWhere(['abnormal' => 2]);
        
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
            $query->andFilterWhere(['between', 'price_time', $start, $end]);
        }
        
        // 订单状态
        if($this->goods_price_state) {
            $query->andFilterWhere(['&', 'goods_price_state', $this->goods_price_state]);
        }
        // 代收
        $query->andFilterWhere(['collection' => 1]);
        
        //垫付
        if($this->advance) {
            $query->join('LEFT JOIN','order_third_advance as advance','advance.order_id = logistics_order.order_id');
            if ($this->advance == 3) {
                $query->andWhere(['is','advance.id', NULL]);
            } else {
                $query->andFilterWhere(['advance.state' => $this->advance]);
            }
        }
        
        if($this->order_state==1) {
            $query->andFilterWhere(['>=','order_state',50]);
        } else if($this->order_state==2) {
            $query->andFilterWhere(['<','order_state',50]);
        }
        
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
    
    public function getAdvanceShow($order_id)
    {
        $model = OrderThirdAdvance::findOne(['order_id'=>$order_id]);
        if($model){
            if($model->state==1){
                return '已垫付';
            }else if($model->state==2){
                return '已收款';
            }
        }else{
            return '未垫付';
        }
    }
    
    /**
     * 代收款收款地址
     * @return string
     */
    public function getGoodsFreightUrl() {
        if($this->same_city == 1) {
            return '?r=teller/income-driver-confirm';
        } else if ($this->same_city == 2) {
            return '?r=teller/income-terminus-confirm';
        }
    }
    
    /**
     * 垫付按钮显示条件
     * @return boolean
     */
    public function getAdvanceDisplay() {
        $advance = OrderThirdAdvance::findOne(['order_id' => $this->order_id]);
        if(!$advance) {
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
     * 代收款按钮显示条件
     * @return boolean
     */
    public function getGoodsDisplay() {
        if($this->order_state < Yii::$app->params['returnOrderStateDriver']) {
            return false;
        }
        
        $advance = OrderThirdAdvance::findOne(['order_id' => $this->order_id]);
        if($advance && $advance->state == 1) {
            if($this->same_city == 1) {
                $model = new IncomeDriver();
            } else if ($this->same_city == 2) {
                $model = new IncomeTerminus();
            }
            return !$model->getIsConfirm(ArrayHelper::toArray($this));
        }
        return false;
    }
    
    public function getAdvanceAddUser() {
        $advance = OrderThirdAdvance::findOne(['order_id'=>$this->order_id]);
        if($advance && $advance->add_user) {
            return AdminUser::findOne($advance->add_user)->user_truename;
        }
        return '';
    }
    
    public function getAdvanceAddTime() {
        $advance = OrderThirdAdvance::findOne(['order_id'=>$this->order_id]);
        if($advance && $advance->add_time) {
            return date('Y-m-d H:i', $advance->add_time);
        }
        return '';
    }
    
    public function getRemark() {
        return Html::button('查看修改备注', ['id'=>'remark_'.$this->order_id,'class' => 'btn btn-info remark_edit','data-id'=>$this->order_id,'data-toggle' => 'modal','data-target' => '#remark-modal']);
    }

}
