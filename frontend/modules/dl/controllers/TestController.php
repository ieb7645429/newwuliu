<?php

namespace frontend\modules\dl\controllers;

use Yii;
use frontend\modules\dl\models\LogisticsOrder;
use frontend\modules\dl\models\LogisticsReturnOrder;
use frontend\modules\dl\models\OrderTime;
use frontend\modules\dl\models\ReturnOrderTime;
use frontend\modules\dl\models\UserBalance;
use frontend\modules\dl\models\ApplyForWithdrawal;
use frontend\modules\dl\models\WithdrawalLog;
use frontend\modules\dl\models\BalanceLog;

class TestController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	if($_GET['debug'] !='shan')
    	{
    		echo "有问题!";
    		die;
    	}
    	if(!isset($_GET['member_id']))
    	{
    		echo "也有问题!";
    		die;
    	}
    	$member_id = $_GET['member_id'];
    	echo '用户id:'.$member_id;
    	echo "<br>";
    	echo '修改的订单编号';
    	echo "<br>";
        set_time_limit(0);
        // 主账号放到第一位
        $del = array(
        		$member_id,
        );
        if(isset($_GET['del_id']))
        {
        	$del = array(
        			$member_id,
        			$_GET['del_id']
        	);
        }
        if($member_id < 1) {
            echo "请修改member_id";
            die;
        }
        $userBalance = new UserBalance();
        LogisticsOrder::updateAll(['collection_poundage_one'=>0, 'collection_poundage_two' => 0], [ 'member_id' =>$member_id]);
        if(count($del) > 1) {
            UserBalance::deleteAll(['in' ,'user_id', array_slice($del, 1)]);
        }
        
        // 更新余额为0
        $u = $userBalance->findOne([$member_id]);
        if($u){
            $u->user_amount=0;
            $u->withdrawal_amount=0;
            $u->is_withdrawal = 0;
            $u->save();
        }
        
        // 删除原有记录
        BalanceLog::deleteAll(['in' ,'uid', $del]);
        WithdrawalLog::deleteAll(['in' ,'uid', $del]);
        
        if(count($del) > 1) {
            LogisticsOrder::updateAll(['member_id'=>$member_id], ['in', 'member_id', array_slice($del, 1)]);
        }
        $datas = LogisticsOrder::find()->where('member_id = '. $member_id . ' and order_state > 20 and collection = 1')->asArray()->all();
        foreach ($datas as $data) {
            echo $data['order_id']."<br>";
            $orderTime = OrderTime::findOne($data['order_id']);
            // 进入余额
//             $userBalance -> addUserBalanceInfo($data['order_id']);
            // 更新log时间为封车时间
//             $blog = BalanceLog::find()->where('order_sn = "' . $data['logistics_sn']  . '" and type = 1 and source_type = 1')->one();
//             $blog->add_time = $orderTime['ruck_time'];
//             $blog->update();
            
            // 返货
//             if ($data['return_logistics_sn']) {
//                 $rOrder = LogisticsReturnOrder::findOne(['logistics_sn' => $data['return_logistics_sn']]);
                
//                 $userBalance->returnBalanceInfo($rOrder['order_id']);
                
//                 $rOrderTime = ReturnOrderTime::find()->innerJoin('logistics_return_order', 'logistics_return_order.order_id = return_order_time.order_id')
//                                        ->where('logistics_return_order.logistics_sn = "' . $data['return_logistics_sn']. '"')->one();
//                 // 更新log时间为返货入库时间
//                 $blog = BalanceLog::find()->where('order_sn = "' . $rOrder['logistics_sn'] . '" and type = 2 and source_type = 2')->one();
//                 $blog->add_time = $rOrderTime['ruck_time'];
//                 $blog->update();
//             }
            
            // 进入可提现
            if ($data['goods_price_state'] & 1) {
                $userBalance->editUserWithdrawalAmountInfo($data['order_id']);

                // 更新可提现时间为财务收款时间
                $wlog = WithdrawalLog::find()->where('order_sn = "' . $data['logistics_sn'] . '" and type = 1')->one();
                $wlog->add_time = $orderTime['income_price_time'];
                $wlog->update();
            }
        }
        // 用户提现
        if(count($del) > 1) {
            ApplyForWithdrawal::updateAll(['user_id'=>$member_id], ['in', 'user_id', array_slice($del, 1)]);
        }
        $logs = ApplyForWithdrawal::find()->where('user_id = ' . $member_id)->asArray()->all();
        foreach ($logs as $log) {
            $this->editwithdrawal($log['amount'], $member_id);
            // 更新余额log时间为财务收款时间
//             $blog = BalanceLog::find()->where('uid = ' . $member_id . ' and type = 2 and source_type = 3')->orderBy('add_time desc')->one();
//             $blog->add_time = $log['add_time'];
//             $blog->update();
            // 更新可提现时间为财务收款时间
            $wlog = WithdrawalLog::find()->where('uid = ' . $member_id . ' and type = 2')->orderBy('add_time desc')->one();
            $wlog->add_time = $log['add_time'];
            $wlog->update();
        }
        die('end');
    }
    
    /**
     * 用户提现，信息整理，增加log
     * @param unknown $withdrawalPrice
     * @param unknown $userId
     */
    public function editwithdrawal($withdrawalPrice, $userId)
    {
        $modelUserBalance = UserBalance::findBySql("select * from user_balance where user_id = $userId")->one();
        $modelApplyForWithdrawal = new ApplyForWithdrawal();
//         if($modelUserBalance->withdrawal_amount< $withdrawalPrice || $modelUserBalance->user_amount < $withdrawalPrice)
//         {
//             return false;
//         }
        $modelWithdrawalLog = new WithdrawalLog();
        $modelBalanceLog = new BalanceLog();
        $applyForWithdrawal = new ApplyForWithdrawal();
        $userb = new UserBalance();
        $res1 = $modelWithdrawalLog->addWithdrawalLog($userId, $withdrawalPrice, $modelUserBalance->withdrawal_amount, $modelUserBalance->withdrawal_amount-$withdrawalPrice,0,2);
        $res2 = $modelBalanceLog->addBalancelLog($userId, $withdrawalPrice, $modelUserBalance->user_amount, $modelUserBalance->user_amount-$withdrawalPrice);

        $res4 = $userb->editUserBalance($modelUserBalance, $withdrawalPrice);
        if($res1 && $res2 && $res4)
        {
            return true;
        }
        return false;
    }
    


}
