<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "balance_log".
 *
 * @property string $id
 * @property string $uid 用户id
 * @property string $amount 订单价钱
 * @property string $before_amount 前余额
 * @property string $after_amount 后余额
 * @property string $content 内容
 * @property int $type 类型（1收入，2支出）
 * @property int $source_type (1发货，2反货，3提现)
 * @property string $order_sn 票号
 * @property string $add_time 添加时间
 */
class BalanceLog extends \yii\db\ActiveRecord
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
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_dl');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'content', 'type', 'order_sn', 'add_time'], 'required'],
            [['uid', 'type', 'source_type', 'add_time'], 'integer'],
            [['amount', 'before_amount', 'after_amount'], 'number'],
            [['content'], 'string'],
            [['order_sn'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户',
            'amount' => '金额',
            'before_amount' => '操作前余额',
            'after_amount' => '操作后余额',
            'content' => '内容',
            'type' => 'Type',
            'source_type' => 'Source Type',
            'order_sn' => '票号',
            'add_time' => '时间',
        ];
    }
    
    /**
     * 增加余额log
     * 朱鹏飞
     */
    public function addBalancelLog($userId, $goodsPrice, $beforeAmount, $afterAmount, $order_sn = '0')
    {
    	if($order_sn != '0')
    	{
    		$str = substr($order_sn, 0, 1);
    		if($str === 'F'){//反货
    			$type = 2;
    			$source_type = 2;
    			$content = '反货';
    		}elseif ($str === 'T'){//退货
    			$type = 2;
    			$source_type = 2;
    			$content = '退货';
    		}else {
    			$type = 1;
    			$source_type = 1;
    			$content = '开单';
    		}
    	}else{//入进可提现余额
    		$type = 2;
    		$source_type = 3;
    		$content = '提现';
    	}
    	$this->uid = $userId;
    	$this->amount = $goodsPrice;
    	$this->before_amount = $beforeAmount;
    	$this->after_amount = $afterAmount;
    	$this->content = $content;
    	$this->type = $type;
    	$this->source_type = $source_type;
    	$this->order_sn = "$order_sn";
    	$this->add_time = time();
    	return $this->save();
    }
    /**
     * 修改货值添加log
     */
    public function editBalancelLog($params,$content = '修改'){
        if($params['model']->order_state<=10) return true;
        $amount = $params['after_amount']-$params['before_amount'];
        $before_amount = UserBalance::findOne($params['model']->member_id)->user_amount;
        $after_amount = $before_amount + $amount;
        if($after_amount<0){
            return false;
        }
        $this->uid = $params['model']->member_id;
        $this->amount = abs($amount);
        $this->before_amount = $before_amount;
        $this->after_amount = $after_amount;
        $this->content = $content;
        $this->type = $amount>0?1:2;
        $this->source_type = 1;
        $this->order_sn = $params['model']->logistics_sn;
        $this->add_time = time();
        return $this->save();
    }
    
    /**
     * 获得余额操作类型
     * 靳健
     */
    public function getSourceType($id){
        switch ($id){
            case 1 : return '发货'; break;
            case 2 : return '返货'; break;
            case 3 : return '提现'; break;
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
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
}
