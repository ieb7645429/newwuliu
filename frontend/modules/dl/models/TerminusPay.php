<?php
namespace frontend\modules\dl\models;

use Yii;
use yii\helpers\ArrayHelper;
use frontend\modules\dl\models\LogisticsOrderSearch;
use frontend\modules\dl\models\LogisticsOrder;
use backend\modules\dl\models\IncomeTerminus;

class TerminusPay extends IncomeTerminus
{
    /**
     * 取得订单列表
     * @param unknown $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLogisticsOrderList($params) {
        $query = LogisticsOrder::find();
        
        $this->load($params);

        $query->innerJoin('order_time', 'logistics_order.order_id = order_time.order_id')
              ->leftJoin('order_remark', 'logistics_order.order_id = order_remark.order_id');
        $query->select('logistics_order.*, order_time.price_time, order_time.unload_time, order_remark.terminus_content');

        // 外阜
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
        // 外阜订单
        $query->andFilterWhere(['same_city' => 2]);
        // 订单状态为已完成
        $query->andFilterWhere(['=', 'order_state' , Yii::$app->params['returnOrderStateDelivery']]);
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