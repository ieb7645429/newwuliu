<?php

namespace frontend\modules\dl\controllers;

use Yii;
use frontend\modules\dl\models\WithdrawalLog;
use frontend\modules\dl\models\WithdrawalLogSearch;
use mdm\admin\components\MenuHelper;
use frontend\modules\dl\models\BankInfo;
use frontend\modules\dl\models\UserBalance;
use yii\base\Exception;
use frontend\modules\dl\models\ApplyForWithdrawal;
use frontend\modules\dl\models\LogisticsOrder;
use frontend\modules\dl\models\ApplyForWithdrawalSearch;
use frontend\modules\dl\models\WithdrawalOrder;

class MemberPayController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new WithdrawalLogSearch();
        $withdrawalLog = new WithdrawalLog(['scenario' => 'search']);
        
        $add_time = $this->getAddTime(Yii::$app->request->post('WithdrawalLog')['withdrawal_time']);
        $dataProvider = $searchModel->orderSearch(Yii::$app->request->queryParams,$add_time,'over');
        $withdrawal_amount = empty(UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount)?0:UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount;
        return $this->render('index',[
                    'searchModel' => $searchModel,
                    'withdrawalLog' => $withdrawalLog,
                    'dataProvider' => $dataProvider,
                    'withdrawal_amount' => $withdrawal_amount,
                    'add_time' => $add_time,
                    'menus' => $this->_getMenus(),
                ]);
    }
    
    public function actionPayable(){
        $searchModel = new WithdrawalLogSearch();
        $withdrawalLog = new WithdrawalLog(['scenario' => 'search']);
        $logisticsOrder = new LogisticsOrder();
        $balance = new UserBalance();
        try{//判断用户是否通需要变为订单提现
            $tr = Yii::$app->db->beginTransaction();
            $withDrawalType = $this->getUserType();
            $orderWithDrawal = $this->isGoToOrder($withDrawalType);

            if($orderWithDrawal){
                $re1 = $logisticsOrder->orderToWithdrawal();//遍历订单到withdrawal_order状态为已提现
                $re2 = $logisticsOrder->orderWithdrawalPriceState();//改变订单goods_price_state状态
                $re3 = $balance->userTypeEdit();//改变用户提现方式
                if(!$re1||!$re2||!$re3){
                    throw new Exception('错误', '408');
                }
                $withDrawalType = 1;
            }
            $tr->commit();
        }catch(Exception $e){
            $tr->rollBack();
            $withDrawalType = $this->getUserType();
        }
        
        
        $withdrawal_amount = empty(UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount)?0:UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount;
        
        $add_time = $this->getAddTime(Yii::$app->request->post('WithdrawalLog')['add_time']);
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->orderSearch($params,$add_time);
        $withdrawal_amount = empty(UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount)?0:UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount;
        if($withDrawalType==1){
            return $this->render('payable-another',[
                    'searchModel' => $searchModel,
                    'withdrawalLog' => $withdrawalLog,
                    'dataProvider' => $dataProvider,
                    'type' => $withDrawalType,
                    'withdrawal_amount' => $withdrawal_amount,
                    'add_time' => $add_time,
                    'menus' => $this->_getMenus(),
            ]);
        }else{
            return $this->render('payable',[
                    'searchModel' => $searchModel,
                    'withdrawalLog' => $withdrawalLog,
                    'dataProvider' => $dataProvider,
                    'type' => $withDrawalType,
                    'withdrawal_amount' => $withdrawal_amount,
                    'add_time' => $add_time,
                    'menus' => $this->_getMenus(),
                    'withdrawal_amount' => $withdrawal_amount,
            ]);
        }
        
    }
    
    public function actionPaylist(){
        $searchModel = new ApplyForWithdrawalSearch();
        $applyForWithdrawal = new ApplyForWithdrawal(['scenario' => 'search']);
        
        $add_time = $this->getAddTime(Yii::$app->request->post('ApplyForWithdrawal')['add_time']);
        $dataProvider = $searchModel->historySearch(Yii::$app->request->queryParams,$add_time);
        return $this->render('paylist',[
                'searchModel' => $searchModel,
                'applyForWithdrawal' => $applyForWithdrawal,
                'dataProvider' => $dataProvider,
                'add_time' => $add_time,
                'menus' => $this->_getMenus(),
                ]);
    }
    
    public function actionDetail(){
        $searchModel = new WithdrawalLogSearch();
        $withdrawalLog = new WithdrawalLog(['scenario' => 'search']);
        $add_time = $this->getAddTime(Yii::$app->request->post('WithdrawalLog')['add_time']);
        $dataProvider = $searchModel->allSearch(Yii::$app->request->queryParams,$add_time,'over');
        $withdrawal_amount = empty(UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount)?0:UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount;
        return $this->render('detail',[
                'searchModel' => $searchModel,
                'withdrawalLog' => $withdrawalLog,
                'dataProvider' => $dataProvider,
                'add_time' => $add_time,
                'menus' => $this->_getMenus(),
                'withdrawal_amount' => $withdrawal_amount
        ]);
    }
    
    
    /**
     * 提现处理
     * @throws Exception
     */
    public function actionWithDrawal(){
        $model = new UserBalance();
		$bank  = new BankInfo();
		$widthDrawalLog = new WithdrawalLog();
		$withDrawalOrder = new WithdrawalOrder();
        try{
            $tr = Yii::$app->db->beginTransaction();
            $amount = empty(UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount)?0:UserBalance::findOne(Yii::$app->user->id)->withdrawal_amount;
            if(!empty(Yii::$app->request->post('order_arr'))){
                $IsWithdrawal = $withDrawalOrder->isWithdrawal(Yii::$app->request->post('order_arr'));
                if($IsWithdrawal){
                    throw new Exception('订单已经提现', '1');
                }
                $amount = 0;
                foreach(Yii::$app->request->post('order_arr') as $key=>$value){
                    $widthDrawal = $widthDrawalLog::findOne(['order_sn'=>$value]);
                    $amount += $widthDrawal->amount;
                }
            }
                
                
           
            $res = $model->editwithdrawal($amount,Yii::$app->user->id);
            $bankinfo = $bank->getBankInfoByUser(Yii::$app->user->id);
			//var_dump($bankinfo);
			if(empty($bankinfo)){
                throw new Exception('请填写银行卡信息', '1');
            }
			if($res === false){
                throw new Exception('提现失败', '1');
            }
            $stateRes = $model->withdrawalStateEdit(Yii::$app->request->post('order_arr'));
            if(!$stateRes){
                throw new Exception('状态修改失败', '1');
            }
            $tr->commit();
            $result = ['error'=>0,'message'=>'提现成功'];
        }catch(Exception $e){
            $tr->rollBack();
			$result = ['error'=>1,'message'=>$e->getMessage()];
        }
        return json_encode($result);
    }
    /**
     * ajax获取订单详情
     */
    public function actionAjaxOrderDetails(){
        $id = $_POST['id'];
        $withdrawalLog = new WithdrawalLog();
        $withdrawalModel = $withdrawalLog::findOne($id);
        if($withdrawalModel->type==1){//判断收入支出
            $list = $this->getIncomeDetail($withdrawalModel->order_sn);
        }else{
            $list = $this->getOutDetail($withdrawalModel->uid,$withdrawalModel->add_time);
        }
        $html = $this->getDetailHtml($list);
        return $html;
    }
    
    
    private function getUserType(){
        $userBalance = UserBalance::findOne(Yii::$app->user->id);
        if(empty($userBalance)||empty($userBalance->is_withdrawal)) return 0;
        return $userBalance->is_withdrawal;
    }
    
    private function isGoToOrder($type){
        $userBalance = UserBalance::findOne(Yii::$app->user->id);
        if(empty($userBalance)) return false;
        if($type==0&&$userBalance->withdrawal_amount<1){
            return true;
        }
        return false;
    }

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
                'index' => ['menu' => '/member-pay/payable', 'item' => '/member-pay/index'],
                'payable' => ['menu' => '/member-pay/payable', 'item' => '/member-pay/payable'],
                'paylist' => ['menu' => '/member-pay/payable', 'item' => '/member-pay/paylist'],
                'detail' => ['menu' => '/member-pay/payable', 'item' => '/member-pay/detail'],
        );
    
        return $arr[Yii::$app->controller->action->id];
    }
    private function getWithDrawalType(){
        $userBalance = UserBalance::findOne(Yii::$app->user->id);
        if(empty($userBalance)){
            return 0;
        }
        if(empty($userBalance->is_withdrawal)){
            $userBalance->is_withdrawal = 0;
            if($userBalance->withdrawal_amount<1){
                $userBalance->is_withdrawal = 1;
                $userBalance->save();
            }
            return $userBalance->is_withdrawal;
        }
        return $userBalance->is_withdrawal;
    }
    
    /**
     * 靳健
     * 添加时间筛选条件
     * @param unknown $time
     * @return unknown|string
     */
    private function getAddTime($time){
        if(!empty($time)){
            list($start, $end) = explode(' - ', $time);
            $add_time['start'] = strtotime($start);
            $add_time['end'] = strtotime($end)+60*60*24;
            $add_time['date'] = $time;
            //print_r($add_time);die;
            return $add_time;
        }
        return '';
    }
    
    /**
     * 获取收入明细
     * @param unknown $order_sn
     */
    private function getIncomeDetail($order_sn){
        $list = array();
        $list[$order_sn] = $this->getOrderSn($order_sn);
        return $list;
    }
    
    /**
     * 获取支出明细
     * @param unknown $uid
     * @param unknown $add_time
     */
    private function getOutDetail($uid,$add_time){
        $list = array();
        $applyModel = new ApplyForWithdrawal();
        $withdrawalModel = new WithdrawalOrder();
        $model = $applyModel->find()->where(['user_id'=>$uid,'add_time'=>$add_time])->one();
        if(empty($model)) return $list;
        $orderModel = $withdrawalModel->find()->where(['apply_id'=>$model->id])->all();
        if(empty($orderModel)) return $list;
        foreach($orderModel as $key => $value){
            $list[$value['order_sn']] = $this->getOrderSn($value['order_sn']);
        }
        return $list;
    }
    /**
     * 获取友件编号
     * @param unknown $order_sn
     */
    private function getOrderSn($order_sn){
        $logisticsOrder = new LogisticsOrder();
        $orderModel = $logisticsOrder::findOne(['logistics_sn'=>$order_sn]);
        if(!empty($orderModel->order_sn)){
            if(!is_numeric($orderModel->order_sn)){
                 return unserialize($orderModel->order_sn);
            }else{
                 return $orderModel->order_sn;
            }
        }else{
            return  '';
        }
    }
    /**
     * 订单详细表格
     * @param unknown $list
     */
    private function getDetailHtml($list){
        $str = '';
        $str .= '<table class="table table-striped">';
        $str .= '<tr>
                    <th data-width="120">票号</th>
                    <th>订单编号</th>
                </tr>';
        foreach($list as $key => $value){
            $str .= '<tr>';
            $str .= '<td>'.$key.'</td>';
            $str .= '<td>'.$this->getValueFormat($value).'</td>';
            $str .= '</tr>';
        }
        $str .= '</table>';
        return $str;
    }
    /**
     * 友件编号格式化
     */
    private function getValueFormat($value){
        $arr = explode(',',$value);
        $i = 1;
        $n = count($arr);
        $str = '';
        foreach($arr as $key => $value){
            if($i!=$n){
                if($i%3==0){
                    $str .= $value.'<br/>';
                }else{
                    $str .= $value.',';
                }
            }else{
                $str .= $value;
            }
            $i++;
        }
        return $str;
    }
}
