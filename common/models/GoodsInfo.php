<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "goods_info".
 *
 * @property int $id
 * @property int $order_id 订单Id
 * @property string $name 商品名称
 * @property int $number 商品数量
 * @property string $price 商品价钱
 */
class GoodsInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_info';
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
            'name' => 'Name',
            'number' => 'Number',
            'price' => 'Price',
        ];
    }
    
    /**
     * 添加商品详细信息
     * @param int $orderId 订单id
     * @param array $name 商品名
     * @param array $number 商品数量
     * @param array $price 商品价钱
     * @return true false
     */
    public function addGoodsInfo($orderId, $name, $number ,$price){
        foreach ($name as $k => $v)
        {
            $model = new GoodsInfo();
            $model->order_id = $orderId;
            $model->name = $v;
            $model->number = $number[$k];
            $model->price = $price[$k];
            $model->save();
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
