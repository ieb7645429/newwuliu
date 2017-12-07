<?php

namespace frontend\modules\dl\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "apply_for_withdrawal".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $amount 申请提现金额
 * @property string $add_time 时间
 */
class ApplyForWithdrawal extends \yii\db\ActiveRecord
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
        return 'apply_for_withdrawal';
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
            [['user_id', 'add_time'], 'required'],
            [['user_id', 'add_time'], 'integer'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户名',
            'amount' => '金额',
            'status' => '状态',
            'add_time' => '申请时间',
            'order_sn' => '提现订单',
        ];
    }
    
    /**
     * 用户名
     * @return \yii\db\ActiveQuery
     */
    public function getUserTrueName() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * 银行信息
     * @return \yii\db\ActiveQuery
     */
    public function getBankInfo() {
        return $this->hasOne(BankInfo::className(), ['user_id' => 'user_id']);
    }
    
    public function getStatusName() {
        if($this->status == 1) {
            return '未付款';
        } else if ($this->status == 2) {
            return '已付款';
        }
    }
    
    public function add($userId, $amount){
    	$this->user_id = $userId;
    	$this->amount = $amount;
    	$this->status = 1;
    	$this->add_time = time();
    	return $this->save();
    }
    
    public function edit($id){
    	$data = self::findOne($id);
    	if($data->status !=1){
    		return false;
    	}
    	$data->status = 2;
    	return $data->save();
    }
    
    public function edit3(){
    	$data = self::findOne($id);
    	if($data->status !=1){
    		return false;
    	}
    	$data->status = 3;
    	return $data->save();
    }
    
    
    public function getShowStatus($status){
        switch ($status){
            case 1 : return '申请提现'; break;
            case 2 : return '同意提现'; break;
            case 3 : return '提现失败'; break;
            default : return '未知'; break;
        }
    }
    
    /**
     * 靳健
     * 获取用户最新提现记录id
     */
    public function getNewId(){
        return $this::find()->where(['user_id'=>Yii::$app->user->id])->max('id');
    }
    /**
     * 靳健  获取相关提现订单
     * @param unknown $id
     */
    public function getWithdrawalOrder($id){
        $withdrawalOrder = new WithdrawalOrder();
        $all = $withdrawalOrder->find()->where(['apply_id'=>$id])->asArray()->all();
        if(empty($all)) return '';
        $arr = array();
        foreach($all as $key => $value){
            $arr[] = $value['order_sn'];
        }
        $str = implode(',',$arr);
        return $str;
    }
}
