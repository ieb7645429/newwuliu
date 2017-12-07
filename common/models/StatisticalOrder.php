<?php

namespace common\models;

use Yii;
use common\models\Goods;

/**
 * This is the model class for table "statistical_order".
 *
 * @property string $add_time
 * @property string $freight 运费
 * @property string $goods_price 货值
 * @property string $shipping_sale 运费优惠价
 * @property int $same_city 是否同城(1是,2不是)
 * @property int $terminus_id 落地点id
 * @property int $logistics_route_id 线路id
 */
class StatisticalOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statistical_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['add_time'], 'required'],
            [['add_time', 'same_city', 'terminus_id', 'logistics_route_id'], 'integer'],
            [['freight', 'goods_price', 'shipping_sale'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'add_time' => 'Add Time',
            'freight' => 'Freight',
            'goods_price' => 'Goods Price',
            'shipping_sale' => 'Shipping Sale',
            'same_city' => 'Same City',
            'terminus_id' => 'Terminus ID',
            'logistics_route_id' => 'Logistics Route ID',
        ];
    }
    
    /**
     * 开单时增加app时时查询记录
     * @param unknown $data
     */
    public function add($data)
    {
    	$this->logistics_sn = $data->logistics_sn;
    	$this->add_time = time();
    	$this->freight = $data->freight;
    	$this->goods_price = $data->goods_price;
    	$this->shipping_sale = $data->shipping_sale;
    	$this->same_city = $data->same_city;
    	$this->terminus_id = $data->terminus_id;
    	$this->logistics_route_id = $data->logistics_route_id;
    	$this->collection = $data->collection == 1? 1 :0;
    	$this->goods_num = $data->goods_num;
    	$this->save();
    }
    
    /**
     * 获取总信息
     * @param unknown $where
     */
    public function getTotalInfo($where)
    {
    	$res1 = $this->getInfo($where);
    	$res2 = $this->getInfo(array());
    	return array('whereInfo'=>$res1, 'total'=>$res2);
    }
    
    /**
     * 条件查询
     * @param unknown $where
     */
    private  function getInfo($where)
    {
    	$info = StatisticalOrder::find()
    	->select('count(*) as num, sum(goods_num) as goods_num, sum(collection) as collection, sum(freight) as freight, sum(goods_price) as goods_price, sum(shipping_sale) as shipping_sale')
    	->where($where)
    	->asArray()
    	->one();
	foreach ($info as $k1 => $v1)
	{
	    $info[$k1] = is_null($v1)?'0':$v1;
	 }
    	$price = ($info['freight']) - ($info['shipping_sale']);
    	$info['enfreight'] = "$price";
	if($info['num'] == 0)
	{
		$info['proportion'] = '0'."%";
	}else{
		$info['proportion'] = round((($info['collection']/$info['num'])*100))."%";
	}
    	return $info;
    }
    
    /**
     * 修改订单统计表
     * 朱鹏飞
     * @param strint $logisticsSn 票号,
     * @param Object $data 订单对像,
     */
    public function edit($logisticsSn,$data)
    {
    	$info = StatisticalOrder::findOne($logisticsSn);
    	if(empty($info))
    	{
    		return;
    	}
    	$info->freight = $data->freight;
    	$info->goods_price = $data->goods_price;
    	$info->shipping_sale = $data->shipping_sale;
    	$info->same_city = $data->same_city;
    	$info->terminus_id = $data->terminus_id;
    	$info->logistics_route_id = $data->logistics_route_id;
    	$info->collection = $data->collection == 1? 1 :0;
    	$info->goods_num = $data->goods_num;
    	$info->save();
    }
    
    /**
     * 删除订单统计表
     * 朱鹏飞
     * @param unknown $logisticsSn
     */
    public function del($logisticsSn)
    {
    	StatisticalOrder::deleteAll(['logistics_sn'=>$logisticsSn]);
    }
    
    /**
     * 订单统计
     */
    public function getEmployeeCount(){
        $count = array();
        $sameCityWhere = ['same_city'=>1];
        $count['order_num'] = $this::find()->count();
        $count['goods_num'] = $this::find()->sum('goods_num');
        $count['price_count'] = $this::find()->where(['and',['>','goods_price',0]])->count();
        $count['price'] = $this::find()->sum('goods_price');
        $count['same_city_order'] = $this::find()->where(['and',$sameCityWhere])->count();
        $count['same_city_goods'] = $this::find()->where(['and',$sameCityWhere])->sum('goods_num');
        $count['same_city_price_count'] = $this::find()->where(['and',['>','goods_price',0],$sameCityWhere])->count();
        $count['same_city_price'] = $this::find()->where(['and',$sameCityWhere])->sum('goods_price');
        return $count;
    }
}
