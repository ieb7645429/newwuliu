<?php

namespace frontend\modules\hlj\models;
 
use Yii;

/**
 * This is the model class for table "logistics_route".
 *
 * @property int $logistics_route_id
 * @property string $logistics_route_code 地区字母编号(SY)
 * @property string $logistics_route_no 地区数字编号（001）
 * @property string $logistics_route_name 路线名称
 * @property int $member_provinceid 省id
 * @property int $member_cityid 市id
 * @property int $member_areaid 区id
 */
class LogisticsRoute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_route';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_hlj');
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logistics_route_code', 'logistics_route_no', 'logistics_route_name'], 'string', 'max' => 155],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'logistics_route_id' => 'Logistics Route ID',
            'logistics_route_code' => 'Logistics Route Code',
            'logistics_route_no' => 'Logistics Route No',
            'logistics_route_name' => 'Logistics Route Name',
        ];
    }
    
    public function getLogisticsRouteData($logisticsRouteId)
    {
    	$LogisticsRouteInfo = $this->getLogisticsRouteInfo(array('logistics_route_id'=>$logisticsRouteId));
    	$LogisticsRoute[$LogisticsRouteInfo['0']['logistics_route_id']] = $LogisticsRouteInfo['0']['logistics_route_code'].$LogisticsRouteInfo['0']['logistics_route_no'].'('.$LogisticsRouteInfo['0']['logistics_route_name'].')';
    	return $LogisticsRoute;
    }
    
    /**
     * 获取物流线路下拉列表信息
     */
    public function getLogisticsRoute($city_id =107,$city_type = 1){
        $LogisticsRouteInfo = $this->getLogisticsRoutexianlu($city_id,$city_type);
        if(!empty($LogisticsRouteInfo)){
            foreach ($LogisticsRouteInfo as $K => $v){
                $LogisticsRoute[$v['logistics_route_id']] = $v['logistics_route_code'].$v['logistics_route_no'].'('.$v['logistics_route_name'].')';
            }
        }else{
            $LogisticsRoute[] = '暂无信息';
        }
        return $LogisticsRoute;
    }
    /**
     * ajax回接路线option
     * 靳健
     */
    public function ajaxLogisticsRoute($city_id, $city_type) {
        $condition = array();
        if($city_type == 2) {
            $condition['city_id'] = $city_id;
            $condition['area_id'] = 0;
        } else if($city_type == 3) {
            $condition['area_id'] = $city_id;
        }
        $data = self::find()->innerJoin('logistics_area', 'logistics_route.logistics_route_id = logistics_area.logistics_route_id')
                            ->where($condition)
                            ->asArray()
                            ->all();
        
        $str = '';
        if(!empty($data)){
            foreach ($data as $v){
                $str .= "<option value='".$v['logistics_route_id']."'>".$v['logistics_route_code'].$v['logistics_route_no'].'('.$v['logistics_route_name'].')'."</option>";
            }
        }
        return $str;
    }
    
    /**
     * 查询物流线路信息
     * @param unknown $condition
     */
    public function getLogisticsRouteInfo($condition){
        return $this::find()->where($condition)->asArray()->all();
    }
    
    /**
     * 以主键为条件查询物流线路表
     * @param unknown $id
     * @return \common\models\LogisticsRoute
     */
    public function getLogisticsRouteFindOne($id)
    {
    	return self::findOne($id);
    }
    
    /**
     * 查询物流线路
     * @param unknown $condition
     */
    public function getLogisticsRoutexianlu($cityId,$city_type){
        if($city_type == 1){
            $where = ['city_id'=>$cityId,'area_id'=>0];
        }else if($city_type == 2){
            $where = ['area_id'=>$cityId];
        }
    	$data = $this->find()->innerJoin('logistics_area', 'logistics_route.logistics_route_id = logistics_area.logistics_route_id')
    	->where($where)
    	->asArray()
    	->all();
    	return $data;
    }
    /**
     * 确认司机路线
     * @param $member_id  
     * */
    public function getDriverRouteId($member_id)
    {
        $driver = new Driver();
        $logistics_car_id = $driver::find()->where(['member_id'=>$member_id])->asArray()->one()['logistics_car_id'];
        $LogisticsCar = new LogisticsCar();
        $logistics_route_id = $LogisticsCar::find()->where(['logistics_car_id'=>$logistics_car_id])->asArray()->one()['logistics_route_id'];
        return $logistics_route_id;
    }
    /**
     * 确定落地点路线
     * @param $member_id
     */
    public function getTerminusRouteId($member_id)
    {
        $terminusUser = new TerminusUser();
        $terminus_id = $terminusUser::find()->where(['user_id'=>$member_id])->asArray()->one()['terminus_id'];
        $terminus = new Terminus();
        $logistics_route_id = $terminus::find()->where(['terminus_id'=>$terminus_id])->asArray()->one()['logistics_route_id'];
        return $logistics_route_id;
    }
    /**
     * 路线下拉列表
     * @params $same_city 是否同城
     */
    public function getRouteDropList($same_city = 2){
        $routeList = array();
        $routeList = yii\helpers\ArrayHelper::map($this::find()->where(['same_city'=>$same_city])->all(),'logistics_route_id','logistics_route_name');
        return $routeList;
        
    }


    /**
     * @desc 根据Id取得 logistics_route_name
     * @param unknown $id
     * @return string
     */
    public static function getLogisticsRouteNameById($id) {
        $area = static::findOne($id);
        if($area) {
            return $area->logistics_route_name;
        }
        return '';
    }


}
