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
use common\models\UserBalance;

class IncomeLogisticsSn extends LogisticsOrderSearch
{
    /**
     * 取得订单列表
     * @param unknown $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getLogisticsOrder($logisticsSn) {
        $query = LogisticsOrder::find();

        // 不为测试数据
        $query->andFilterWhere(['test' => 2]);

        $query->andFilterWhere(['like', 'logistics_sn', $logisticsSn]);

        return $query->asArray()->one();
    }
    
    public function formatData($order) {
        if(empty($order)) {
            return array('code'=>404, 'msg'=>'订单不存在！');
        }
//         if ($order['order_state'] < Yii::$app->params['orderStateDriver']) {
//             return array('code'=>400, 'msg'=>'订单未封车！');
//         }

        if ($order['same_city'] == 1) {
            $model = new IncomeDriver();
        } else {
            $model = new IncomeTerminusNot();
        }

//         if($model->getIsConfirm($order)) {
//             return array('code'=>300, 'msg'=>'订单已经收款或不需要收款！');
//         }
        $amount = $model->_getAmount($order);
        $order['all_amount'] = $amount['all_amount'];
        return array('code'=>200, 'msg'=>'成功！', 'datas'=>$order);
    }
}