<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "terminus".
 *
 * @property int $terminus_id
 * @property int $logistics_route_id 物流线路表id
 * @property string $terminus_name 落地点名
 * @property int $terminus_provinceid 落地省id
 * @property int $terminus_cityid 落地点市id
 * @property int $terminus_areaid 落地点区id
 * @property string $terminus_ area 落地点详细地址
 * @property string $add_time 生成时间
 */
class Terminus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'terminus';
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
            [['logistics_route_id', 'terminus_provinceid', 'terminus_cityid', 'terminus_areaid'], 'integer'],
            [['terminus_name', 'terminus_ area', 'add_time'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'terminus_id' => 'Terminus ID',
            'logistics_route_id' => 'Logistics Route ID',
            'terminus_name' => 'Terminus Name',
            'terminus_provinceid' => 'Terminus  Provinceid',
            'terminus_cityid' => 'Terminus  Cityid',
            'terminus_areaid' => 'Terminus Areaid',
            'terminus_ area' => 'Terminus  Area',
            'add_time' => 'Add Time',
        ];
    }
    
    /**
     * 获取落点下拉列表信息
     * @param number $city_id
     */
    public function getTerminus($city_id = 107){
        $terminusInfo = $this::find()->where(['terminus_cityid'=>$city_id])->asArray()->all();
        if(!empty($terminusInfo)){
            foreach ($terminusInfo as $k => $v){
                $terminusArray[$v['terminus_id']] = $v['terminus_name'];
            }
        }else{
            $terminusArray[] = '暂无信息';
        }
        return $terminusArray;
    }
    /**
     * ajax回接路线option
     * 靳健
     */
    public function ajaxTerminus($logistics_route_id){
        $list = $this->getCityTerminus($logistics_route_id);
        $str = '';
        foreach($list as $k => $v){
            $str .= "<option value='".$k."'>".$v."</option>";
        }
        return $str;
    }
    /**
     * 获取落点下拉列表信息
     * @param number $area_id
     */
    public function getCityTerminus($logistics_route_id){
        $terminusInfo = $this::find()->where(['logistics_route_id'=>$logistics_route_id])->asArray()->all();
        if(!empty($terminusInfo)){
            foreach ($terminusInfo as $k => $v){
                $terminusArray[$v['terminus_id']] = $v['terminus_name'];
            }
        }else{
            $terminusArray[] = '暂无信息';
        }
        return $terminusArray;
    }
    
    /**
     * @desc 根据ID取落地点名
     * @author 暴闯
     */
    public static function getNameById($id) {
        $r = self::findOne($id);
        if($r) {
            return $r->terminus_name;
        }
        return '';
    }
    
    /**
     * @desc 根据ID取落地点运费分成比例
     * type = 1 发货 ，type = 2 退货
     * @author 暴闯
     */
    public static function getScaleById($id, $type = 1) {
        $terminus = self::findOne($id);
        if($type == 1) {
            return $terminus->shipping_scale;
        } else if($type == 2) {
            return $terminus->receiving_scale;
        }
        
    }
    
    public function getFictitiousTerminusId() {
        $query = self::find() -> where('is_fictitious = 1')
                              -> select('terminus_id')
                              -> asArray();
        return ArrayHelper::map($query->all(), 'terminus_id', 'terminus_id');
    }
    
    public function Terminus($id){
    	$arr = array();
    	$data = $this->getTerminusInfo($id);
    	$arr[$data['terminus_id']] = $data['terminus_name'];
    	return $arr;
    }
    
    public function getTerminusInfo($id){
    	$data = $this::findOne($id);
    	return ArrayHelper::toArray($data);
    }
}
