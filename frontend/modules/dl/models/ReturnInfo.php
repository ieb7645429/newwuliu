<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "return_info".
 *
 * @property int $id
 * @property int $order_id 退货单Id
 * @property string $name 退货商品名称
 * @property int $number 退货商品数量
 * @property string $price 退货商品价钱
 */
class ReturnInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_info';
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
            [['order_id', 'name'], 'required'],
            [['order_id', 'number'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'name' => '商品名',
            'number' => '商品数量',
            'price' => '商品价钱',
        ];
    }
    
    public function getReturnInfoByOrderId($orderId) {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->andFilterWhere(['order_id' => $orderId]);
        
        return $dataProvider;
    }
    
    /**
     * 朱鹏飞
     * 批量插入退货详细信息
     * @param unknown $orderId
     * @param array() $name 商品名
     * @param array() $number 商品数量
     * @param array() $price 商品价钱
     * @return boolean|number true;成功 false 失败
     */
    public function setReturnInfo($orderId, $name, $number ,$price){
    	$orderPrice = 0;
    	foreach ($name as $k =>$v){
    		if($v && $number[$k] && $price[$k]){
    			$this->order_id = $orderId;
    			$this->name = $v;
    			$this->number = $number[$k];
    			$this->price = $price[$k];
    			$orderPrice +=$number[$k]*$price[$k];
    			if(!$this::validate())
    			{
    				return false;
    			};
    			$data = array('order_id'=>$orderId, 'name'=>$v, 'number'=>$number[$k], 'price'=>$price[$k]);
    			$r = $this->addReturnInfo($data);
    			if(!$r){
    				return false;
    			}
    		}
    	}
    	return $orderPrice;
    
    }
    
    /**
     * 增加
     */
    public function addReturnInfo($upda){
    	$model = new ReturnInfo();
    	$model->setAttributes($upda);
    	return $model->save();
    }
    
    /**
     * 删除货品
     * @param unknown $orderId
     */
    public function delReturnInfoByOrderId($orderId) {
    	$returnInfo = $this::findAll(['order_id'=>$orderId]);
    	if($returnInfo){
    		return $this::deleteAll('order_id = :order_id', [':order_id' => $orderId]);
    	}
    	return true;
    }
	/**
     *  查询goods信息
	 *  @Id 订单id
	 **/
	 public static function getGoodsInfoById($Id){
	    $text = '';
		$data =  static::find()->where('order_id =:id',[':id'=>$Id])->asArray()->all();
		if(is_array($data) && !empty($data)){
	       for($i=0;$i<count($data);$i++){
		     $text.="商品名称:".$data[$i]['name'];
			 $text.="商品数量:".$data[$i]['number'];
			 $text.="商品价钱:".$data[$i]['price'].'|';
		   }	
		   $text = substr($text,0,strlen($text)-1);
		}
        return $text;
	 }
    
}
