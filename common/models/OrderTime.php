<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_time".
 *
 * @property int $order_id
 * @property string $price_time 物流价钱生成时间
 * @property string $sorter_time 分捡时间
 * @property string $ruck_time 装车时间
 * @property string $unload_time 卸货时间
 * @property string $signed_for_time 签收时间
 */
class OrderTime extends \yii\db\ActiveRecord
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
        return 'order_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_time', 'sorter_time', 'ruck_time', 'unload_time', 'signed_for_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'price_time' => 'Price Time',
            'sorter_time' => 'Sorter Time',
            'ruck_time' => 'Ruck Time',
            'unload_time' => 'Unload Time',
            'signed_for_time' => 'Signed For Time',
        ];
    }

    
    /**
     * 更新物流时间表
     * @param unknown $upda
     * @param array $condition
     */
    public function upOrderTime($upda = array(), $condition = array()){ 
        return $this::updateAll($upda, $condition);
    }
    
    /**
     * 判断更新时间
     * @param unknown $state
     * @param unknown $orderId
     */
    public function orderTimeswitch($state, $data)
    {
        $timeArray = array('50'=>'ruck_time', '10'=>'price_time', '70'=>'unload_time', '80'=>'signed_for_time', 'collection'=>'collection_time' ,'freight'=>'income_freight_time','price'=>'income_price_time' , 'pay_freight_time'=>'pay_freight_time', 'pay_price_time'=>'pay_price_time');
        return $this->upOrderTime(array($timeArray[$state]=>time()), array('order_id'=>$data->order_id));
    }


    /**
     * @desc 根据Id取得 物流价钱生成时间
     * @param unknown $id
     * @return string
     */
    public static function getPriceTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->price_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 分捡时间
     * @param unknown $id
     * @return string
     */
    public static function getSorterTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->sorter_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 装车时间
     * @param unknown $id
     * @return string
     */
    public static function getRuckTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->ruck_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 卸货时间
     * @param unknown $id
     * @return string
     */
    public static function getUnloadTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->unload_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 签收时间
     * @param unknown $id
     * @return string
     */
    public static function getSignedForTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->signed_for_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 落地点收款时间
     * @param unknown $id
     * @return string
     */
    public static function getCollectionTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->collection_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 财务收运费时间
     * @param unknown $id
     * @return string
     */
    public static function getIncomeFreightTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->income_freight_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 财务付运费时间
     * @param unknown $id
     * @return string
     */
    public static function getPayFreightTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->pay_freight_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 财务收货款时间
     * @param unknown $id
     * @return string
     */
    public static function getIncomePriceTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->income_price_time;
        }
        return '';
    }
    /**
     * @desc 根据Id取得 财务付货款时间
     * @param unknown $id
     * @return string
     */
    public static function getPayPriceTimeById($id) {
//        header("Content-Type:text/html;charset=utf-8");
        $area = static::findOne($id);
//        var_dump($area);exit();
        if($area) {
            return $area->pay_price_time;
        }
        return '';
    }
}
