<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrder;
use common\models\Area;
use common\models\LogisticsOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;
use common\models\OrderTime;
use common\models\BuyInfo;
use yii\web\Response;
use common\models\GoodsInfo;
use common\models\User;
use common\models\Terminus;
use common\models\LogisticsRoute;
use mdm\admin\components\MenuHelper;
use Symfony\Component\Console\Terminal;

/**
 * LogisticsOrderController implements the CRUD actions for LogisticsOrder model.
 */
class MemberController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LogisticsOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LogisticsOrderSearch();
        $dataProvider = $searchModel->searchMemberOrder(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * Lists all LogisticsOrder models.
     * @return mixed
     */
    public function actionIndexOver()
    {
        $searchModel = new LogisticsOrderSearch();
        $type = 'over';
        $dataProvider = $searchModel->searchMemberOrder(Yii::$app->request->queryParams, $type);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * Displays a single LogisticsOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'menus' => $this->_getMenus(),
        ]);
    }
    
    public function actionPrint(){
        if(empty(Yii::$app->request->get('order_id'))){
            return $this->redirect(['index']);
        }else{
            $list = new LogisticsOrder();
            $order_arr = explode(',',Yii::$app->request->get('order_id'));
            $where = ['in','logistics_order.order_id',$order_arr];
            $orderList = $list->UserPrint($where);
            $orderList = $list->getGoodsPrice($orderList,'member');
            $orderList = $this->num_Amount($orderList);
        }
        return $this->renderpartial('print',['orderList'=>$orderList]);
        
        
    }

    
    /**
     * 个人用户生成发货单
     */
    public function actionCreateOrder(){
        $model = new LogisticsOrder();
        $modelBuyInfo = new BuyInfo();
        $modelOrderTime = new OrderTime();
        $modelGoodsInfo = new GoodsInfo();
        $user = new User();
        if ($model->load(Yii::$app->request->post())) {
        $tr = Yii::$app->db->beginTransaction();
            $type = 'member';//录入类型
            $model->fillLogisticsInfo($model, $type, Yii::$app->request->post('User')['username']);
            try {
                $model->save();
                $model->logistics_sn = $model->getLogisticsSn($model->order_id);//票号
                $model->save();
//                 $modelBuyInfo->addBuyInfo($model);
                $modelOrderTime->order_id=$model->order_id;
                $modelOrderTime->save();//增加订单时间表
                if(Yii::$app->request->post()['GoodsInfo']['name'] && Yii::$app->request->post()['GoodsInfo']['number'] && Yii::$app->request->post()['GoodsInfo']['price']){
                    $modelGoodsInfo->addGoodsInfo($model->order_id, Yii::$app->request->post()['GoodsInfo']['name'], Yii::$app->request->post()['GoodsInfo']['number'], Yii::$app->request->post()['GoodsInfo']['price']);
                }
                if($model->order_sn)
                {
                	$res =$model->editYoujianOrderState(array('orderSn'=>$model->order_sn));//修改友件网订单状态
                	if($res === false)
                	{
                		throw new Exception('商品详细信息添加失败', '404');
                	}
                }
                $tr -> commit();
            } catch (Exception $e) {
            	$tr->rollBack();
                Yii::$app->session->setFlash('error', '添加发货单失败！');
            }
            return $this->redirect(['view', 'id' => $model->order_id]);
        } else {
            $area = new Area();
            return $this->render('create_order', [
                'model' => $model,
                'user' => $user,
                'area' => $area,
                'menus' => $this->_getMenus(),
            ]);
        }
    }

    /**
     * Updates an existing LogisticsOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->order_id]);
        } else {
            $area = new Area();
            $user = new User();
            $username = $user::findOne($model->member_id);
            return $this->render('update', [
                'model' => $model,
                'area' => $area,
                'user' => $username,
                'menus' => $this->_getMenus(),
            ]);
        }
    }

    /**
     * Deletes an existing LogisticsOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionPhoneGetInfo(){
        
        
        
    }
    
    /**
     * 取得menus
     * @return array[]|string[]
     */
    private function _getMenus()
    {
        $menus = MenuHelper::getAssignedMenu(Yii::$app->user->id);
        $items = array();
        
        $activeMenus = $this->_getActiveMenu();
        
        foreach ($menus as &$menu) {
            if ($menu['url'][0] == $activeMenus['menu']) {
                $menu['active'] = 'active';
                if($activeMenus['item'] !== false && isset($menu['items'])) {
                    foreach ($menu['items'] as &$item) {
                        if($item['url'][0] == $activeMenus['item']) {
                            $item['active'] = 'active';
                            break;
                        }
                    }
                    $items = $menu['items'];
                }
            }
        }
        return ['menus' => $menus, 'items' => $items];
    }
    
    private function _getActiveMenu() {
        $arr = array(
            'index' => ['menu' => '/member/index', 'item' => '/member/index'],
            'index-over' => ['menu' => '/member/index', 'item' => '/member/index-over'],
            'create-order' => ['menu' => '/member/index', 'item' => false],
            'view' => ['menu' => '/member/index', 'item' => false],
            'update' => ['menu' => '/member/index', 'item' => false],
        );
        
        return $arr[Yii::$app->controller->action->id];
    }

    /**
     * Finds the LogisticsOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogisticsOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticsOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 朱鹏飞
     * 获取买家信息
     */
    public function actionReceiving()
    {
        Yii::$app->response->format=Response::FORMAT_JSON;
        $model = new BuyInfo();
        $area = new Area();
        $route = new LogisticsRoute();
        $terminus = new Terminus();
        $info = $model->getBuyInfo(Yii::$app->request->post('phone'));
        /*create by feng begin*/
        $receiving_name = Yii::$app->request->post('phone');
        $modelLogisticsOrder = new LogisticsOrder();
        $rate = $modelLogisticsOrder->statisLogisticsOrder('order_id',$receiving_name);

        /*end*/

        
        $route_str = '';
        $terminus_str = '';
        if($info) {
			 //增加判断是否isreceive为1 2017-10-12 小雨
              //  $_isrece = $model->getBuyInfo(Yii::$app->request->post('phone'));
				if($info['is_receive'] == 1){
					return array('code'=>400, 'msg'=>'此号码不能收货');
			 }
            if($info['city_id']) {
                $cityInfo = $area->getAreaInfo(['area_id' => $info['city_id']]);
                $info['city_name'] = $cityInfo->area_name;
            }
            if($info['area_id']) {
                $areaInfo = $area->getAreaInfo(['area_id' => $info['area_id']]);
                if($areaInfo->pinyin_name){
                    $info['area_name'] = $areaInfo->area_name;
                } else {
                    $info['area_id'] = 0;
                }
            }
            $city_id = $info['city_id'];
            $city_type = 2;//层级参数,2为市级
            if($info['area_id'] > 0){
            	$city_id = $info['area_id'];
            	$city_type = 3;//层级参数,3为区级
            }
            //获取线路
            $route_str = $route->ajaxLogisticsRoute($city_id, $city_type);
            //获取落地点
            if($info['logistics_route_id']) {
                $terminus_str = $terminus->ajaxTerminus($info['logistics_route_id']);
                if (strstr($terminus_str, '暂无信息')) {
                    $terminus_str = '';
                }
            }

        } else {
				   return array('code'=>300, 'msg'=>'无记录');
        }
        $data['info'] = $info;
        $data['route_str'] = $route_str;
        $data['terminus_str'] = $terminus_str;
        $data['rate'] = $rate;
        
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$data);
    }
    
    /**
     * 靳健
     * 获取用户信息
     */
    public function actionMemberInfo()
    {
        $model = new User();
        if(!empty(Yii::$app->request->post('phone'))){
            $where = ['member_phone'=>Yii::$app->request->post('phone')];
        }else if(!empty(Yii::$app->request->post('name'))){
            $where = ['username'=>Yii::$app->request->post('name')];
        }
        $info = $model->getMemberInfo($where);
    
        Yii::$app->response->format=Response::FORMAT_JSON;
        return array('code'=>200, 'msg'=>'成功', 'datas'=>$info);
    }
	/**
	*  打印
	*  小雨
	**/
    public function actionPrintSelectedlist(){
        $list = new LogisticsOrder();
        $order_arr = explode(',',Yii::$app->request->post('order_sn'));
        $where = array('in','logistics_order.order_id',$order_arr);
		$orderList = $list->UserPrint($where);
		$orderList = $list->getGoodsPrice($orderList,'member');
        if($orderList){
            $result = [
                    'error'=>0,
                    'data'=>$orderList
            ];
        }else{
            $result = [
                    'error'=>1,
                    'message'=>'打印失败'
            ];
        }
        return json_encode($result);	
	}
	
	public function num_Amount($orderList){
	    foreach($orderList as $key => $value){
	        $orderList[$key]['All_amount'] = $this->get_amount($value['all_amount']);
	    }
	    return $orderList;
	}
	
	//金钱转换
	public function get_amount($num){
	    $c1 = "零壹贰叁肆伍陆柒捌玖";
	    $c2 = "分角元拾佰仟万拾佰仟亿";
	    $num = round($num, 2);
	    $num = $num * 100;
	    if (strlen($num) > 10) {
	        return "数据太长，没有这么大的钱吧，检查下";
	    }
	    $i = 0;
	    $c = "";
	    while (1) {
	        if ($i == 0) {
	            $n = substr($num, strlen($num)-1, 1);
	        } else {
	            $n = $num % 10;
	        }
	        $p1 = substr($c1, 3 * $n, 3);
	        $p2 = substr($c2, 3 * $i, 3);
	        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
	            $c = $p1 . $p2 . $c;
	        } else {
	            $c = $p1 . $c;
	        }
	        $i = $i + 1;
	        $num = $num / 10;
	        $num = (int)$num;
	        if ($num == 0) {
	            break;
	        }
	    }
	    $j = 0;
	    $slen = strlen($c);
	    while ($j < $slen) {
	        $m = substr($c, $j, 6);
	        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
	            $left = substr($c, 0, $j);
	            $right = substr($c, $j + 3);
	            $c = $left . $right;
	            $j = $j-3;
	            $slen = $slen-3;
	        }
	        $j = $j + 3;
	    }
	
	    if (substr($c, strlen($c)-3, 3) == '零') {
	        $c = substr($c, 0, strlen($c)-3);
	    }
	    if (empty($c)) {
	        return "零元整";
	    }else{
	        return $c . "整";
	    }
	}
}
