<?php

namespace frontend\controllers;

use common\models\CreateMemberLog;
use common\models\CreateStateLog;
use Yii;
use common\models\Area;
use common\models\AuthAssignment;
use frontend\models\CreateUserForm;
use yii\base\Exception;
use mdm\admin\components\MenuHelper;
use common\models\User;
use common\models\UserSearch;
use common\models\BankInfo;
use common\yjmodels\Seller;
use common\yjmodels\Store;
use common\yjmodels\Member;
use common\models\UserAll;
use common\models\LogisticsOrder;
use yii\helpers\ArrayHelper;
use common\models\WithdrawalLog;
use common\models\WithdrawalOrder;
use common\models\UserBalance;
use common\models\OrderThirdAdvance;
use common\models\LogisticsReturnOrder;
use common\models\OrderAdvance;
use backend\models\TellerLog;
use backend\models\TellerIncomeSnLog;
use common\models\ReturnOrderTime;
use common\models\OrderTime;

class CreateUserController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->redirect(['search']);
    }
    
    public function actionSearch()
    {
        return $this->render('search',
            ['menus' => $this->_getMenus()]);
    }
    
    public function actionCreate() {

        $model = new CreateUserForm();
        $area = new Area();
        $auth = new AuthAssignment();
        if ($model->load(Yii::$app->request->post())) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $userInfo = User::findByUsername($model->username);
                if ($userInfo) {
                    if (empty($userInfo->small_num)) {
                        $userInfo->small_num = $model->small_num;
                        $userInfo->save();
                    }
                    
                } else {
                    if (!$user = $model->signup()) {
                        var_dump($model->errors);
                        throw new Exception('用户注册失败');
                    }
                    
                    if(!$auth -> saveMember($user->id)) {
                        var_dump($auth->errors);
                        throw new Exception('用户权限设置失败');
                    }
                }

                $tr -> commit();
                Yii::$app->getSession()->setFlash('success', '注册成功!<br>会员小号:'.$model->small_num);
                return $this->render('create', [
                    'model' => new CreateUserForm(),
                    'area' => $area,
                    'menus' => $this->_getMenus()
                ]);
            } catch (Exception $e) {
                $tr->rollBack();
                //                 echo $e->getMessage();die;
                //                 if($e->getMessage() == '用户权限设置失败') {
                Yii::$app->getSession()->setFlash('error', $e->getMessage());
                //                 }
            }
        }
            
        return $this->render('create', [
            'model' => $model,
            'area' => $area,
            'menus' => $this->_getMenus()
        ]);
    }


    
    public function actionUserList(){
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->editSearch(Yii::$app->request->queryParams);

        return $this->render('user-list',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus()
        ]);
    }
    
    public function actionUpdate($id)
    {
        try {
            $model = $this->findModel($id);
        }catch(NotFoundHttpException $e){
            Yii::$app->getSession()->setFlash('error', '用户不存在');
            return $this->redirect(['update','id'=>$id]);
        }
        $bankInfo = new BankInfo();
        $user = new User();
        $userAll = new UserAll();
        $bank = $bankInfo::findOne(['user_id'=>$id]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            try {
            $tr = Yii::$app->db->beginTransaction();
            $tr2 = Yii::$app->db2->beginTransaction();
                $user_post = Yii::$app->request->post('User');
                $userNeedEdit = 0;//判断会员信息是否需要修改
                $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
                //验证会员号,电话格式
                if(in_array(Yii::$app->params['roleMember'], $roles) && !$user->phoneFormat($user_post)){
                    Yii::$app->getSession()->setFlash('error', '会员号必须为手机号!');
                    return $this->redirect(['update','id'=>$id]);
                }

                if($model->username!=$user_post['username']){//判断会员号是否需要修改
                    if(empty($user_post['username'])){
                        Yii::$app->getSession()->setFlash('error', '会员号不能为空!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    if(!$user->issetUserName($user_post['username'])){
                        Yii::$app->getSession()->setFlash('error', '会员号已存在!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    $model->username = $user_post['username'];
                    
                    //更新user_all表
                    $allModel = $userAll::findOne($id);
                    $allModel->username = $user_post['username'];
                    $all_re = $allModel->save();
                    
                    if(!$all_re){
                        Yii::$app->getSession()->setFlash('error', '用户账号修改失败!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    
                    $userNeedEdit = 1;
                }
                
                if($model->user_truename!=$user_post['user_truename']){//判断真实姓名是否需要修改
                    if(empty($user_post['user_truename'])){
                        Yii::$app->getSession()->setFlash('error', '真实姓名不能为空!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    $model->user_truename = $user_post['user_truename'];
                    $userNeedEdit = 1;
                }
                
                if($userNeedEdit==1){
                    $youjian_re = $this->_youjianUserEdit($model);
                    if(!$youjian_re){
                        Yii::$app->getSession()->setFlash('error', '友件信息修改失败!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                }
                $model->member_phone = $user_post['member_phone'];
                $wuliu_re = $model->save();
                
                
                if(!$wuliu_re){
                    Yii::$app->getSession()->setFlash('error', '会员信息修改失败!');
                    return $this->redirect(['update','id'=>$id]);
                }
            
                if(!empty(Yii::$app->request->post('BankInfo'))){
					$bankInfoParam = Yii::$app->request->post('BankInfo');
					if($bankInfo->issetBankInfoCardNo($bankInfoParam['bank_info_id'],$bankInfoParam['bank_info_card_no'])){
					   Yii::$app->getSession()->setFlash('error', '银行卡号已存在!');
					   return $this->redirect(['update','id'=>$id]);
					}                	
                }            
                if(!empty(Yii::$app->request->post('BankInfo'))){
                    $post_bank = Yii::$app->request->post('BankInfo');
                    $bank->bank_info_card_no = $post_bank['bank_info_card_no'];
                    $bank->bank_info_account_name = $post_bank['bank_info_account_name'];
                    $bank->bank_info_bank_name = $post_bank['bank_info_bank_name'];
                    $bank->bank_info_bank_address = $post_bank['bank_info_bank_address'];
                    $bank_result = $bank->save();
                    if(!$bank_result){
                        Yii::$app->getSession()->setFlash('error', '银行卡信息修改失败!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                }
            $tr->commit();
            $tr2->commit();
            Yii::$app->getSession()->setFlash('success', '修改成功!');
            return $this->redirect(['update','id'=>$id]);
            }catch(Exception $e){
            $tr->rollBack();
            $tr2->rollBack();
            Yii::$app->getSession()->setFlash('error', '修改失败');
            return $this->redirect(['update','id'=>$id]);
            }
        } else {
            return $this->render('update', [
                    'model' => $model,
                    'menus' => $this->_getMenus(),
                    'bank' => $bank,
            ]);
        }
    }


    public function actionOrderMemberUpdate() {
        $model = new LogisticsOrder();
        if (Yii::$app->request->isPost) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $logistics_sn = ArrayHelper::getValue(Yii::$app->request->post('LogisticsOrder'), 'logistics_sn');
                $member_phone= ArrayHelper::getValue(Yii::$app->request->post('LogisticsOrder'), 'member_phone');
                if (!$logistics_sn || !$member_phone) {
                    throw new Exception('参数错误！');
                }
                $order = $model::findOne(['logistics_sn'=>Yii::$app->request->post('LogisticsOrder')]);



 /*
* 0.0
* 条件查询得到转订单修改前的信息old_member_id、old_order_state、old_state、old_goods_price_state、值的状态
* */

                header("Content-Type:text/html;charset=utf8");
                $memberId = LogisticsOrder::find()->select('member_id')
                    ->where(['member_id' =>$order['member_id']])
                    ->asArray()
                    ->one();
//                var_dump($memberId['member_id']);die;
                $state = LogisticsOrder::find()->select('state')
                    ->where(['state' =>$order['state']])
                    ->asArray()
                    ->one();
//                var_dump($state['state']);die;
                $orderState = LogisticsOrder::find()->select('order_state')
                    ->where(['order_state' =>$order['order_state']])
                    ->asArray()
                    ->one();
//                var_dump($orderState['order_state']);die;
                $goodsPriceState = LogisticsOrder::find()->select('goods_price_state')
                    ->where(['goods_price_state' =>$order['goods_price_state']])
                    ->asArray()
                    ->one();
//                var_dump($goodsPriceState['goods_price_state']);die;

/*
* 0.0
* 条件查询结束
* */



                if(strpos($order->return_logistics_sn, 'Z') === 0)
                {
                    throw new Exception('追回订单不可改状态！');
                }

                $userModel = new User();
                $user = $userModel->getMemberInfo(['or', ['username'=>$member_phone], ['small_num'=>$member_phone]]);
                if (!$order || !$user) {
                    throw new Exception('数据错误！');
                }
                if($order->member_id == $user->id) {
                    throw new Exception('修改前后的发货人相同！');
                }

                if ($order->goods_price_state & 4) {
                    throw new Exception('用户已经提现，不能修改！');
                }
                
                $oldId = $order->member_id;
                $newId = $user->id;
                
                // 修改订单信息
                $order->member_name = $user->user_truename;
                $order->member_id = $user->id;
                $order->member_cityid = $user->member_cityid;
                $order->member_phone = $user->member_phone;
                if(!$order->save()) {
                    throw new Exception('订单信息修改失败！');
                }



                
                // 已进入用户可提现金额
                if ($order->goods_price_state & 1) {
                    
                    // 修改提现订单表信息
                    $wOrder = WithdrawalOrder::findOne(['order_sn'=>$logistics_sn]);
                    $wOrder->user_id = $newId;
                    if(!$wOrder->save()) {
                        throw new Exception('修改提现订单失败！');
                    }
                    
                    
                    // 修改原始log记录
                    $log = WithdrawalLog::find()->where(['order_sn'=>$logistics_sn])->one();
                    $log->order_sn = '+'.$logistics_sn;
                    if(!$log->save()) {
                        throw new Exception('修改余额log失败！');
                    }
                    
                    // 原卖家追加转订单log
                    $userBalance1 = UserBalance::findOne($oldId);
                    $log1 = new WithdrawalLog();
                    $log1->uid = $oldId;
                    $log1->amount = $log->amount;
                    $log1->before_amount = $userBalance1->withdrawal_amount;
                    $log1->after_amount = $userBalance1->withdrawal_amount - $log->amount;
                    $log1->content = '转订单扣除';
                    $log1->type = 3;
                    $log1->order_sn = '-'.$logistics_sn;
                    $log1->add_time = time();
                    if(!$log1->save()) {
                        throw new Exception('添加转订单log失败！');
                    }


                    // 扣除余额
                    $userBalance1->withdrawal_amount -= $log->amount;
                    if(!$userBalance1->save()) {
                        throw new Exception('减可提现余额失败！');
                    }
                    
                    // 新卖家添加进入可提现log
                    $userBalance2 = UserBalance::findOne($newId);
                    $log2 = new WithdrawalLog();
                    $log2->uid = $newId;
                    $log2->amount = $log->amount;
                    $log2->before_amount = $userBalance2->withdrawal_amount;
                    $log2->after_amount = $userBalance2->withdrawal_amount + $log->amount;
                    $log2->content = '进入可提现';
                    $log2->type = 1;
                    $log2->order_sn = $logistics_sn;
                    $log2->add_time = time();
                    if(!$log2->save()) {
                        throw new Exception('添加新订单log失败！');
                    }
                    // 加余额
                    $userBalance2->withdrawal_amount += $log->amount;
                    if(!$userBalance2->save()) {
                        throw new Exception('加可提现余额失败！');
                    }
                }


                /*
                 * 0.0
                 * 添加 将转订单修改后的信息 保存到 create_member_log
                 * */
                $createMemberLog = new CreateMemberLog();
//                    var_dump($createMemberLog);die;
                $createMemberLog->logistics_sn = $logistics_sn;
                $createMemberLog->member_phone = $member_phone;
//                $createMemberLog->old_member_id = $order->getOldAttribute('member_id')/*$oldId*/;
                $createMemberLog->old_member_id = $memberId['member_id']/*$oldId*/;
                $createMemberLog->new_member_id = $order->member_id;
//                $createMemberLog->old_order_state = $order->getOldAttribute('order_state');
                $createMemberLog->old_order_state = $orderState['order_state'];
                $createMemberLog->new_order_state = $order->order_state;
//                $createMemberLog->old_state = $order->getOldAttribute('state');
                $createMemberLog->old_state = $state['state'];
                $createMemberLog->new_state = $order->state;
//                $createMemberLog->old_goods_price_state = $order->getOldAttribute('goods_price_state');
                $createMemberLog->old_goods_price_state = $goodsPriceState['goods_price_state'];
                $createMemberLog->new_goods_price_state = $order->goods_price_state;
//                $createMemberLog->user_id = Yii::$app->user->id;
                $createMemberLog->user_id = $_SESSION['__id'];
//                    var_dump($createMemberLog);die;
                if (!$createMemberLog->save()){
                    throw new Exception('转订单信息log保存失败！');
                }

/*
* 0.0
* 添加结束
*/

                $tr->commit();
                Yii::$app->getSession()->setFlash('success', '修改成功！');
                return $this->redirect(['order-member-update']);
            } catch (Exception $e) {
                $tr->rollBack();
                Yii::$app->getSession()->setFlash('error', $e->getMessage());
                return $this->render('order-member-update', [
                    'model' => $model,
                    'menus' => $this->_getMenus(),
                ]);
            }
        } else {
            return $this->render('order-member-update', [
                'model' => $model,
                'menus' => $this->_getMenus(),
            ]);
        }
    }
    
    /**
     * 修改订单状态
     * @throws Exception
     * @return \yii\web\Response|string
     */
    public function actionUpdateOrderState(){
        $model = new LogisticsOrder();
        if (Yii::$app->request->isPost) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $logistics_sn = ArrayHelper::getValue(Yii::$app->request->post('LogisticsOrder'), 'logistics_sn');

                if (!$logistics_sn) {
                    throw new Exception('参数错误！');
                }
                $order = $model::findOne(['logistics_sn'=>Yii::$app->request->post('LogisticsOrder')]);

//                header("Content-Type:text/html;charset=utf8");
//                var_dump($order);die;

/*
 * 0.0
 * 条件查询得到状态修改前的old_order_state、old_state、old_goods_price_state、值的状态
 * */


                header("Content-Type:text/html;charset=utf8");
                $state = LogisticsOrder::find()->select('state')
                    ->where(['state' =>$order['state']])
                    ->asArray()
                    ->one();
//                var_dump($state['state']);die;
                $orderState = LogisticsOrder::find()->select('order_state')
                    ->where(['order_state' =>$order['order_state']])
                    ->asArray()
                    ->one();
//                var_dump($orderState['order_state']);die;
                $goodsPriceState = LogisticsOrder::find()->select('goods_price_state')
                    ->where(['goods_price_state' =>$order['goods_price_state']])
                    ->asArray()
                    ->one();
//                var_dump($goodsPriceState['goods_price_state']);die;

/*
 * 0.0
 * 条件查询结束
 * */


                
                if(strpos($order->return_logistics_sn, 'Z') === 0)
                {
                    throw new Exception('追回订单不可改状态！');
                }

                if($order->order_state != 70 && $order->state != 6)
                {
                    throw new Exception('订单未完成！');
                }
                if (!in_array($order ->goods_price_state, array(2,8,9,1))){
                    throw new Exception('订单已收款！');
                }
                $order->state = 2;
                if($order ->goods_price_state == 8)
                {
                    $order ->goods_price_state = 2;
                    
                }
                //订单时间修改
                $orderTime = OrderTime::findOne($order->order_id);
                if(!empty($orderTime))
                {
                    $orderTime->collection_time=0;
                    $orderTime->income_freight_time=0;
                    $orderTime->pay_freight_time=0;
                    $orderTime->income_price_time=0;
                    $orderTime->pay_price_time=0;
                    $orderTime->save();
                }
                //退货订单，退货订单时间删除
                if($order->return_logistics_sn)
                {
                    $reData = LogisticsReturnOrder::find()->where(['logistics_sn'=>$order->return_logistics_sn])->one();
                    $reData->delete();
                    $reOrdertime = ReturnOrderTime::findOne($reData->order_id);
                    $reOrdertime->delete();
                    $order->return_logistics_sn = '';
                }
                $order ->freight_state = 2;
                if($order ->goods_price_state == 9 || $order ->goods_price_state == 1)
                {
                    $order ->goods_price_state = 2;
                    $wLog = new WithdrawalLog();
                    $wLogData = $wLog->find()->where(['order_sn'=>$order->logistics_sn, 'uid'=>$order->member_id])->one();//查进入可提现对应订单的log记录
                    if(empty($wLog))
                    {
                        throw new Exception('订单异常,请联系技术1！');
                    }
                    $wLogData->order_sn = '+'.$order->logistics_sn;
                    $res3 = $wLogData->save();//修改这个记录的订单编号
                    $lastWLogData = $wLog->find()->where(['uid'=>$order->member_id])->orderBy('id desc')->one();//查此用户最后一条log
                    $newWLog = new WithdrawalLog();
                    $newWLog->uid = $order->member_id;
                    $newWLog->amount= $wLogData->amount;
                    $newWLog->before_amount= $lastWLogData->after_amount;
                    $newWLog->after_amount= $lastWLogData->after_amount - $wLogData->amount;
                    $newWLog->content = '系统调整，对余额无影响';
                    $newWLog->type = 3;
                    $newWLog->order_sn= '-'.$order->logistics_sn;
                    $newWLog->add_time= time();
                    $res1 = $newWLog -> save();//新加一条减钱的log
                    $userBD = UserBalance::find()->where(['user_id'=>$order->member_id])->one();
                    $userBD->withdrawal_amount -= $wLogData->amount;
                    $res2 = $userBD->save();//用户可提现余额减去这个订单的钱
                    OrderAdvance::deleteAll(['logistics_sn'=>$order->logistics_sn]);
                    if(!$res1 || !$res2 || !$res3)
                    {
                        throw new Exception('订单异常,请联系技术2！');
                    }
                }
                $datas = OrderThirdAdvance::find()->where(['order_id'=>$order->order_id])->one();
                if($datas)
                {
                    $datas->$datas=1;
                    if(!$datas->save())
                    {
                        throw new Exception('订单异常！');
                    }
                }
                $WOdDatas = WithdrawalOrder::find()->where(['order_sn'=>$order->logistics_sn])->one();
                if($WOdDatas)
                {
                    if($WOdDatas->user_id != $order->member_id)
                    {
                        throw new Exception('订单异常，请联系技术5！');
                    }
                    $res5 = $WOdDatas->delete();
                    if(!$res5)
                    {
                        throw new Exception('订单异常！');
                    }
                }
                $TLDatas = TellerLog::find()->where(['order_id'=>$order->order_id])->one();
                if($TLDatas)
                {
                    if(!$TLDatas->delete())
                    {
                        throw new Exception('订单异常，请联系技术7！');
                    }
                }
                TellerIncomeSnLog::deleteAll(['order_id'=>$order->order_id]);
                if(!$order->save())
                {
                    throw new Exception('修改失败！');
                }

/*
 * 0.0
 * 添加 将修改订单状态后的信息 保存到 create_state_log
 * */
                $CreateStateLog = new CreateStateLog();
                $CreateStateLog->old_order_state =$orderState['order_state'];
                $CreateStateLog->new_order_state = $order->order_state;
                $CreateStateLog->old_state = $state['state'];
                $CreateStateLog->new_state = $order->state;
                $CreateStateLog->old_goods_price_state = $goodsPriceState['goods_price_state'];
                $CreateStateLog->new_goods_price_state = $order->goods_price_state;
                $CreateStateLog->add_time = time();
//              $createMemberLog->user_id = Yii::$app->user->id;
                $CreateStateLog->user_id = $_SESSION['__id'];
//              var_dump(Yii::$app->getSession());die;
                if (!$CreateStateLog->save()){
                    throw new Exception('修改订单状态log保存失败！');
                }

/*
* 0.0
* 添加结束
*/

                $tr->commit();
                Yii::$app->getSession()->setFlash('success', '修改成功！');
                return $this->redirect(['update-order-state']);
            } catch (Exception $e) {
                $tr->rollBack();
                Yii::$app->getSession()->setFlash('error', $e->getMessage());
                return $this->render('update-order-state', [
                        'model' => $model,
                        'menus' => $this->_getMenus(),
                ]);
            }
        }else {
            return $this->render('update-order-state',[
                    'model' => $model,
                    'menus' => $this->_getMenus(),
            ]);
        }
    }
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 取得menus
     * @return array[]|string[]
     */
    private function _getMenus()
    {
        $menus = MenuHelper::getAssignedMenu(Yii::$app->user->id,null,null,true);
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
            'search' => ['menu' => '/create-user/search', 'item' => false],
            'create' => ['menu' => '/create-user/create', 'item' => false],
            'user-list' => ['menu' => '/create-user/user-list', 'item' => false],
            'update' => ['menu' => '/create-user/user-list', 'item' => false],
                'order-member-update' => ['menu' => '/create-user/order-member-update', 'item' => '/create-user/order-member-update'],
            'update-order-state' => ['menu' => '/create-user/order-member-update', 'item' => '/create-user/update-order-state'],
        );
        
        return $arr[Yii::$app->controller->action->id];
    }
    private function _youjianUserEdit($model){
        $member = new Member();
        $seller = new Seller();
        $store = new Store();
        
        $re_store  = $store->storeInfoEdit($model);
        $re_seller = $seller->sellerInfoEdit($model);
        $re_member = $member->memberInfoEdit($model);
        if(!$re_store||!$re_seller||!$re_member){
            return false;
        }
        return true;
    }
 /**
 *  清空银行卡信息
 *  2017-11-23
 *  xiaoyu
 **/
  public function actionUnbind($id)
    {
	    if(empty($id)){
		    Yii::$app->getSession()->setFlash('error', '参数错误');
            return $this->redirect(['user-list','id'=>$id]);
		}
        $bankInfo = new BankInfo();
        $bank = $bankInfo::findOne(['user_id'=>$id]);
        try {
           // $bank->bank_info_card_no = '';
			//$bank->bank_info_account_name = '';
			//$bank->bank_info_bank_name = '';
			//$bank->bank_info_bank_address = '';
			//$bank->bank_info_place = '';//不允许相同都为空
            //$bank->save();
			$bank->delete();
			Yii::$app->getSession()->setFlash('success', '银行卡解绑成功!');
            return $this->redirect(['user-list','id'=>$id]);
            }catch(Exception $e){
            Yii::$app->getSession()->setFlash('error', '银行卡解绑失败');
            return $this->redirect(['user-list','id'=>$id]);
            }
       
    }
}
