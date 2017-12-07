<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "withdrawal_log".
 *
 * @property string $id
 * @property string $uid 用户id
 * @property string $amount 订单价钱
 * @property string $before_amount 前余额
 * @property string $after_amount 后余额
 * @property string $content 内容
 * @property int $type 类型（1收入，2支出）
 * @property string $add_time 添加时间
 */
class WithdrawalLog extends \yii\db\ActiveRecord
{
    const SCENARIO_SEARCH = 'search';
    public $lorder_sn;
    
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
        return 'withdrawal_log';
    }
    
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_dl');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'content', 'type', 'add_time'], 'required'],
            [['uid', 'type', 'add_time'], 'integer'],
            [['amount', 'before_amount', 'after_amount'], 'number'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'amount' => '金额',
            'before_amount' => '操作前余额',
            'after_amount' => '操作后余额',
            'content' => '内容',
            'type' => 'Type',
            'add_time' => '时间',
            'order_sn' => '票号',
        ];
    }
    
    /**
     * 增加余额log
     * 朱鹏飞
     */
    public function addWithdrawalLog($userId, $goodsPrice, $beforeAmount, $afterAmount, $order_sn=0, $type)
    {
    	if($order_sn >0)
    	{
    		$content = '进入可提现';
    	}else
    	{
    		$content = '用户提现';
    	}
    	$this->uid = $userId;
    	$this->amount = $goodsPrice;
    	$this->before_amount = $beforeAmount;
    	$this->after_amount = $afterAmount;
    	$this->content = $content;
    	$this->type = $type;
    	$this->order_sn = "$order_sn";
    	$this->add_time = time();
    	return $this->save();
    }
    public function getType($type){
        switch ($type){
            case 1 : return '收入'; break;
            case 2 : return '支出'; break;
            default : return '未知'; break;
        }
    }
    public function getViewAmount($type){
        if($type==1){
            $str = ' + ';
        }else{
            $str = ' - ';
        }
        return $str;
    
    }
    public function getOrderSn()
    {
        return $this->hasOne(LogisticsOrder::className(), ['logistics_sn' => 'order_sn']);
    }
    public function getAddTime()
    {
        return $this->hasOne(WithdrawalOrder::className(), ['order_sn' => 'order_sn']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    public function getWithdrawalTime($order_sn){
        $withdrawalOrder = new WithdrawalOrder();
        $model = $withdrawalOrder::findOne(['order_sn'=>$order_sn]);
        if(empty($model)||empty($model->add_time)) return '';
        return date('Y-m-d H:i:s',$model->add_time);
    }
    
    /**
     * 支出订单列表
     * @param unknown $uid
     * @param unknown $add_time
     */
    public function OutOrderSn($uid,$add_time){
        $apply = new ApplyForWithdrawal();
        $withdrawalOrder = new WithdrawalOrder();
        $applyModel = $apply->find()->where(['user_id'=>$uid,'add_time'=>$add_time])->one();
        if(empty($applyModel)) return '';
        $orderModel = $withdrawalOrder->find()->where(['in','apply_id',$applyModel->id])->all();
        if(empty($orderModel)) return '';
        $arr = array();
        foreach($orderModel as $key => $value){
            $arr[] = $value['order_sn']; 
        }
        if(count($arr)>3){
            return implode(',',array_slice($arr,0,3)).'...';
        }
        return implode(',',$arr);
    }
}
