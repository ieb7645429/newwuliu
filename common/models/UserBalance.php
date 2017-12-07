<?php

namespace common\models;

use Yii;
use backend\models\PayDealer;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_balance".
 *
 * @property int $id 索引ID
 * @property int $user_id 用户id
 * @property string $user_amount 用户余额
 * @property string $withdrawal_amount 可提现余额
 */
class UserBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_balance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['user_amount', 'withdrawal_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'user_amount' => '余额',
            'withdrawal_amount' => '可提现金额',
        ];
    }
    
    /**
     * 反货信息整理
     * @param unknown $orderId  反货订单id
     */
    public function returnBalanceInfo($orderId)
    {
    	$modelLogisticsOrder = new LogisticsOrder();
    	$modelLogisticsReturnOrder = new LogisticsReturnOrder();
    	$modelPayDealer = new PayDealer();
    	$returnData = $modelLogisticsReturnOrder::find()->where(['order_id'=>$orderId])->asArray()->one();
    	$data = $modelLogisticsOrder->find()->where(['logistics_sn'=>$returnData['ship_logistics_sn']])->asArray()-> one();
    	$returnArr = $modelPayDealer->_getAmount($data);//反货后应得的钱
    	$arr = $modelPayDealer->_getAmount($data,1);//发货应得的钱
    	$kouPrice = $arr['all_amount']-$returnArr['all_amount'];//余额中减去的钱
    	return $this->addReturnBalanceInfo($kouPrice, $returnData['logistics_sn'], $data['member_id']);
    }
    
    /**
     * 原反扣除提现中的钱
     * @param unknown $goodsPrice
     * @param unknown $logisticsSn
     * @param unknown $userId
     */
    public function addReturnBalanceInfo($goodsPrice, $logisticsSn, $userId)
    {
    	$modelUserBalance = self::findOne($userId);
    	return $this->addUserBalance($userId, $modelUserBalance, '-'.$goodsPrice, $logisticsSn);
    }
    
    /**
     * 开单增加提现中的钱
     * @param unknown $model
     */
    public function addUserBalanceInfo($orderId)
    {
    	$modelLogisticsOrder = new LogisticsOrder();
    	$model = $modelLogisticsOrder->findOne($orderId);
    	$modelUserBalance = self::findOne($model->member_id);
    	$modelPayDealer = new PayDealer();
    	$arr = $modelPayDealer->_getAmount(ArrayHelper::toArray($model), 1);
    	
    	if(empty($modelUserBalance))
    	{
    		$modelUserBalance = new UserBalance();
    		$modelUserBalance->is_withdrawal = 1;//新注册的用户为可按订单提现
    	}
    	
    	return $this->addUserBalance($model->member_id, $modelUserBalance, $arr['all_amount'], $model->logistics_sn);
    }
    
    /**
     * 处理用户余额记录
     * 朱鹏飞
     */
    public function addUserBalance($userId, $modelUserBalance, $goodsPrice, $order_sn=0)
    {
    	$modelBalanceLog = new BalanceLog();
    	$modelUserBalance->user_id = $userId;
    	$modelUserBalance->user_amount = $modelUserBalance->getIsNewRecord()?$goodsPrice:$modelUserBalance->getOldAttribute('user_amount')+$goodsPrice;
    	$res = $modelBalanceLog->addBalancelLog($userId, ltrim($goodsPrice, '-'), $modelUserBalance->getIsNewRecord()?0:$modelUserBalance->getOldAttribute('user_amount'), $modelUserBalance->user_amount, $order_sn);
    	if($res === false){
    		return false;
    	}
    	return $modelUserBalance->save();
    }
    
    /**
     * 余额金额进入可提现金额信息整理
     * @param unknown $orderId
     */
    public function editUserWithdrawalAmountInfo($orderId)
    {
    	$modelLogisticsOrder= new LogisticsOrder();
    	$modelWithdrawalLog = new WithdrawalLog();
    	$modelWithdrawalOrder = new WithdrawalOrder();
    	$LogisticsOrder = $modelLogisticsOrder::findOne($orderId);
    	$userBalance = $modelWithdrawalLog->find()->where(['order_sn'=>$LogisticsOrder->logistics_sn])->one();
    	if(!empty($userBalance)){
    		return false;
    	}
    	$modelPayDealer = new PayDealer();
    	$modelUserBalance = self::findOne($LogisticsOrder->member_id);
    	$arr = $modelPayDealer->_getAmount(ArrayHelper::toArray($LogisticsOrder));//订单扣出各种价钱的金额
    	$withdrawalAmount = $arr['all_amount'];//最新可提现金额
    	$modelWithdrawalLog->addWithdrawalLog($modelUserBalance->user_id, $arr['all_amount'], $modelUserBalance->withdrawal_amount, $modelUserBalance->withdrawal_amount + $withdrawalAmount, $LogisticsOrder->logistics_sn, 1);
    	$modelWithdrawalOrder->addWithdrawalOrder($LogisticsOrder->logistics_sn,0,$modelUserBalance->user_id,$withdrawalAmount);
    	return $this->editUserWithdrawalAmount($modelUserBalance, $arr['all_amount']);
    }
    
    /**
     * 余额金额进入可提现金额修改
     * @param unknown $modelUserBalance
     * @param unknown $goodsPrice
     */
    public function editUserWithdrawalAmount($modelUserBalance, $goodsPrice)
    {
    	$modelUserBalance->withdrawal_amount = $modelUserBalance->getOldAttribute('withdrawal_amount')+$goodsPrice;
    	return $modelUserBalance->save();
    }
    
    /**
     * 用户提现，信息整理，增加log
     * @param unknown $withdrawalPrice
     * @param unknown $userId
     */
    public function editwithdrawal($withdrawalPrice, $userId)
    {
    	$modelUserBalance = self::findBySql("select * from user_balance where user_id = $userId for update")->one();
    	$modelApplyForWithdrawal = new ApplyForWithdrawal();
    	$withdrawalPrice = (string)$withdrawalPrice;
    	if($modelUserBalance->withdrawal_amount< $withdrawalPrice)
    	{
    		return false;
    	}
    	$modelWithdrawalLog = new WithdrawalLog();
    	$modelBalanceLog = new BalanceLog();
    	$res1 = $modelWithdrawalLog->addWithdrawalLog($userId, $withdrawalPrice, $modelUserBalance->withdrawal_amount, $modelUserBalance->withdrawal_amount-$withdrawalPrice,0,2);
    	$res2 = $modelBalanceLog->addBalancelLog($userId, $withdrawalPrice, $modelUserBalance->user_amount, $modelUserBalance->user_amount-$withdrawalPrice);
    	$res3 = $modelApplyForWithdrawal->add($userId, $withdrawalPrice);
    	$res4 = $this->editUserBalance($modelUserBalance, $withdrawalPrice);
    	if($res1 && $res2 && $res3 && $res4)
    	{
    		return true;
    	}
    	return false;
    }
    
    /**
     * 用户提现，修改用户余额表
     * @param unknown $modelUserBalance
     * @param unknown $withdrawalPrice
     */
    public function editUserBalance($modelUserBalance, $withdrawalPrice)
    {
    	$modelUserBalance->user_amount = $modelUserBalance->user_amount-$withdrawalPrice;
    	$modelUserBalance->withdrawal_amount = $modelUserBalance->withdrawal_amount-$withdrawalPrice;
    	return $modelUserBalance->save();
    }
    

    /**
     * 修改余额
     * @param unknown $uid
     * @param unknown $before_amount
     * @param unknown $after_amount
     */
    public function editUserAmount($params){
        if($params['model']->order_state<=10) return true;//未封车不修改余额表
        $amount = $params['after_amount'] - $params['before_amount'];
        $model = $this::findOne($params['model']->member_id);
        if($model->user_amount + $amount<0){
            return false;
        }
        $model->user_amount = $model->user_amount + $amount;
        return $model->save();
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function userTypeEdit(){
        $user = $this::findOne(Yii::$app->user->id);
        $user->is_withdrawal = 1;
        return $user->save();
    }
    /**
     * 靳健
     * 提现状态修改
     * @param unknown $order_sn
     */
    public function withdrawalStateEdit($order_arr){
        $orderWithdrawal = new WithdrawalOrder();
        $logisticsOrder = new LogisticsOrder();
        if(empty($order_arr)) return true;
        foreach($order_arr as $key => $value){
            //withdrawal_order表修改
            $re1 = $orderWithdrawal->editWithdrawalOrder($value);
            //order表goods_price_state修改
            $re2 = $logisticsOrder->goodsPriceStateToFive($value);
            if(!$re1||!$re2){
                return false;
            }
        }
        
        return true;
    }
}
