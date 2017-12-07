<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "small_print".
 *
 * @property int $id
 * @property string $print_time
 * @property int $print_member_id
 */
class SmallPrint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'small_print';
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
            [['print_time', 'print_member_id'], 'required'],
            [['print_member_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'print_time' => 'Print Time',
            'print_member_id' => 'Print Member ID',
        ];
    }
    
    /**
     * 添加小码单打印记录
     * @param unknown $data
     */
    public function addSmallHistory($data){
        
        try{
            $tr = Yii::$app->db->beginTransaction();
                $this->print_time = time();
                $this->print_member_id = Yii::$app->user->id;
                $this->save(); 
                $arr = $this->getSmallValue($data);
                $smallOrder = new SmallPrintOrder();
                $smallOrder->id = $this->id;
                $smallOrder->print_content = json_encode($arr);
                $re2 = $smallOrder->save();
            $tr -> commit();
        }catch(Exception $e){
            $tr->rollBack();
        }
    }
    
    /**
     * 获取小码单有用信息
     * @param unknown $data
     */
    public function getSmallValue($data){
        $arr = array();
        foreach($data as $key => $value){
            $arr[$key]['logistics_sn'] = $value['logistics_sn'];
            $arr[$key]['goods_price'] = $value['goods_price'];
            $arr[$key]['freight'] = $value['freight'];
            $arr[$key]['make_from_price'] = $value['make_from_price'];
            $arr[$key]['shipping_sale'] = $value['shipping_sale'];
            $arr[$key]['receiving_name'] = $value['receiving_name'];
        }
        $result['time'] = date('Y/m/d',time());
        $result['data'] = $arr;
        return $result;
    }
}
