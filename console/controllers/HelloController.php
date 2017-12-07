<?php
namespace console\controllers;
use yii\console\Controller;
use common\models\UserBalance;
use common\models\LogisticsOrder;
use common\models\AutoPayment;
use yii\base\Exception;
use Yii;
class HelloController extends Controller
{
    /**
     *所有财务点同意付款的记录，在早上7点修改为进入可提现记录
     *执行时间，早上7点
     */
    public function actionGoodsPriceStateNine()
    {
        $modelUB = new UserBalance();
        $modelLO = new LogisticsOrder();
        $datas = $modelLO->find()
        ->where(['goods_price_state'=>8,'abnormal'=>2, 'test'=>2, 'collection'=>1]);
        if(!empty($datas))
        {
            foreach ($datas->each() as $v)
            {
                if(date('Ymd', $v->add_time) == date('Ymd'))
                {
                    continue;
                }
                try {
                    $tr1 = Yii::$app->db->beginTransaction();
                    $res = $modelUB->editUserWithdrawalAmountInfo($v->order_id);//进入可提现
                    $old = $v->goods_price_state;
                    $v->goods_price_state = $v->goods_price_state | 1;//变成9
                    $res1 = $v->save();
                    if(!$res || !$res1)
                    {
                        throw new Exception('执行错误,订单id:'.$v->order_id, '405');
                    }
                    $tr1->commit();
                } catch (Exception $e) {
                    $tr1->rollBack();
                    $modelAP = new AutoPayment();
                    $modelAP->order_id= $v->order_id;
                    $modelAP->add_time= time();
                    $modelAP->goods_price= $v->goods_price;
                    $modelAP->goods_price_state= $old;
                    $modelAP->save();
                }
            }
        }
    }
    
    /**
     * 所有同城不代收，状态为已封车的订单状态改为已完成，并且开单时间大于24小时
     * 每天晚上八点执行
     */
    public function actionStateSix()
    {
        $modelLO = new LogisticsOrder();
        $datas = $modelLO->find()
        ->where(['order_state'=>70,'state'=>2, 'test'=>2, 'collection'=>2, 'abnormal'=>2, 'same_city' => 1])
        ->andWhere(['<','add_time',time()-(60*60*24)]);
        foreach ($datas->each()as $v)
        {
            $v->state = 6;
            $v->save();
        }
        echo 2;
    }
}