<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrder;
use common\yjmodels\Order;
use common\models\ApiToken;
use yii\filters\NoCsrf;
use yii\filters\VerbFilter;
use common\models\ApiOrder;
use common\yjmodels\OrderCommon;
use common\yjmodels\Address;
use common\yjmodels\Area;

class ApiController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'csrf' => [
                    'class' => NoCsrf::className(),
                    'controller' => $this,
                    'actions' => [
                            'get-order-info',
                    ]
            ],
            'verbs' => [
                    'class' => VerbFilter:: className(),
                    'actions' => [
                            'get-order-info'  => ['post'],
                    ],
            ],
        ];
    }
    
    public function actionGetOrderInfo()
    {
        $modelAo = new ApiOrder();
        $modelAo->ip= $_SERVER["REMOTE_ADDR"];
        $modelAo->add_time = time();
        if(empty(Yii::$app->request->post('orderSn')) ||
                empty(Yii::$app->request->post('token')) ||
                empty(Yii::$app->request->post('company')) ||
                empty(Yii::$app->request->post('type')) ||
                empty(Yii::$app->request->post('freight'))
                )
        {
            $modelAo->code=10001;
            $modelAo->save();
            return $this->get_jsoncode(10001,'','参数为空');
            die;
        }
        $freight = Yii::$app->request->post('freight');
        $modelAT = new ApiToken();
        $apiData = $modelAT::find()->where(['company'=>Yii::$app->request->post('company'),'type'=>Yii::$app->request->post('type')])->one();
        if(empty($apiData))
        {
            $modelAo->code=10004;
            $modelAo->save();
            return $this->get_jsoncode(10004,'','无此用户');
            die;
        }
        $orderSn = Yii::$app->request->post('orderSn');
        
        $token = md5("$apiData->company"."$freight"."$apiData->token"."$apiData->type"."$orderSn");
        if($token !== Yii::$app->request->post('token'))
        {
            $modelAo->code=10005;
            $modelAo->save();
            return $this->get_jsoncode(10005,'','token错误');
            die;
        }
        if(strlen($orderSn) != 16 || !preg_match("/^\d*$/",$orderSn))
        {
            $modelAo->code=10002;
            $modelAo->save();
            return $this->get_jsoncode(10002,'','参数异常');
            die;
        }
        $orderData = Order::find()->where(['order_sn'=>$orderSn])->one();
        if(empty($orderData))
        {
            $modelAo->code=10003;
            $modelAo->save();
            return $this->get_jsoncode(10003,'','记录为空');
            die;
        }
        if($orderData->order_state != 10)
        {
            $modelAo->code=10006;
            $modelAo->save();
            return $this->get_jsoncode(10006,'','订单已发车');
            die;
        }
        $tr = Yii::$app->db->beginTransaction();
        $tr2 = Yii::$app->db2->beginTransaction();
        $orderData->order_state = 70;
        if(!$orderData->save())
        {
            $tr2->rollBack();
            $modelAo->code=10007;
            $modelAo->save();
            return $this->get_jsoncode(10007,'','服务器异常');
            die;
        }
        
        $modelAo->order_sn = $orderSn;
        $modelAo->order_amount= $orderData->order_amount;
        $modelAo->freight= Yii::$app->request->post('freight');
        if(!$modelAo->save())
        {
            $tr->rollBack();
            $modelAo->code=10008;
            $modelAo->save();
            return $this->get_jsoncode(10008,'','服务器异常');
            die;
        }
        $orderComData = OrderCommon::findOne($orderData->order_id);
        $addRessData = Address::findone($orderComData->address_id);
        $addressInfo = array();
        $modelArea = new Area();
        if(!empty($addRessData))
        {
            $addressInfo['address'] = "$addRessData->area_info"."$addRessData->address";
            $addressInfo['consignee'] = $addRessData->true_name;
            $zone = $modelArea->findOne($addRessData->area_id);
            $addressInfo['zone'] = '';
            if(!empty($zone))
            {
                $addressInfo['zone'] = $zone->area_name;
            }
            $city= $modelArea->findOne($addRessData->city_id);
            $province= $modelArea->findOne($city->area_parent_id);
            $addressInfo['province'] = $province->area_name;
            $addressInfo['city'] = $city->area_name;
            $addressInfo['consignee_phone'] = $addRessData->mob_phone;
            
        }
        $addressInfo['amount'] = $orderData->order_amount;
        $tr -> commit();
        $tr2 -> commit();
        $modelAo->code=10000;
        $modelAo->save();
        return $this->get_jsoncode(10000,$addressInfo,'成功');
    }
    
    private function get_jsoncode($code,$datas,$msg=''){
        return json_encode(array('code'=>$code,'datas'=>$datas,'msg'=>$msg));
    }
}
