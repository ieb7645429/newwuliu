<?php
namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\LogisticsOrderSearch;
use common\models\LogisticsOrder;
use backend\models\IncomeDriver;

class DriverPay extends IncomeDriver
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
            // 收款时间
            ['between', 'collection_time', $start, $end],
            // 收款状态为已收款
            ['&', 'state', Yii::$app->params['orderReceived']],
            // 代收 或者 不代收并且运费为提收
            ['or', ['collection' => 1], ['and',['shipping_type' => 1],['!=', 'collection',  '1'],]]
        ];
        $query->andFilterWhere($condition);

        return $query->asArray()->all();
    }
}