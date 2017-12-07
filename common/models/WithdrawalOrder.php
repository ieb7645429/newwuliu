<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "withdrawal_order".
 *
 * @property int $id
 * @property string $logistics_sn
 * @property string $add_time
 * @property int $is_withdrawal 0未体现 1已提现
 */
class WithdrawalOrder extends \yii\db\ActiveRecord
{
    const SCENARIO_SEARCH = 'search';
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEARCH] = [];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'withdrawal_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_time'], 'required'],
            [['add_time', 'is_withdrawal'], 'integer'],
            [['order_sn'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => '票号',
            'amount' => '金额',
            'add_time' => 'Add Time',
            'is_withdrawal' => 'Is Withdrawal',
        ];
    }
    
    /**
     * 靳健
     * 可提现订单记录插入
     * $type 1,修改  2,导入
     */
    public function addWithdrawalOrder($order_sn,$type = 0,$user_id,$amount){
        if(empty($this::findOne(['order_sn'=>$order_sn]))){
                $this->order_sn = $order_sn;
                $this->add_time = 0;
                $this->is_withdrawal = $type;
                $this->user_id = $user_id;
                $this->amount = $amount;
                return $this->save();
        }else{
            if($type==1){
                $this->editWithdrawalOrder($order_sn);
            }
        }
        return true;
    }
    /**
     * 可提现订单状态修改
     * @param unknown $order_sn
     */
    public function editWithdrawalOrder($order_sn){
        $apply = new ApplyForWithdrawal();
        $userBalance = new UserBalance();
        if($userBalance::findOne(Yii::$app->user->id)->is_withdrawal==1)//订单提现状态 添加关联提现记录
            $apply_id = $apply->getNewId();
        else
            $apply_id = 0;
        $orderWithdrawal = $this::findOne(['order_sn'=>$order_sn]);
        $orderWithdrawal->is_withdrawal = 1;
        $orderWithdrawal->add_time = time();
        $orderWithdrawal->apply_id = $apply_id;
        return $orderWithdrawal->save();
    }
    
    /**
     * 判断订单是否已经体现
     */
    public function isWithdrawal($order_sn_arr){
        foreach($order_sn_arr as $key => $value){
            $re = $this::find()->where(['order_sn'=>$value,'is_withdrawal'=>1])->one();
            if($re) return true;
        }
        return false;
    }
    
    /**
     * 关联查询
     * @return ActiveQuery
     */
    public function getOrderSn()
    {
        return $this->hasOne(LogisticsOrder::className(), ['logistics_sn' => 'order_sn']);
    }
}
