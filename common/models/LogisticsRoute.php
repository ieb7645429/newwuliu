<?php

namespace common\models;
 
use Yii;
use common\models\LogisticsCar;
use common\models\Driver;
use common\models\LogisticsArea;

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logistics_route_code', 'logistics_route_no', 'logistics_route_name'], 'string', 'max' => 155],
            [['logistics_route_id'], 'safe'],
            [['logistics_route_name', 'logistics_route_code', 'logistics_route_no', 'same_city'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'logistics_route_code' => '地区字母编号',
            'logistics_route_no' => '地区数字编号',
            'logistics_route_name' => '路线名称',
            'same_city' => '是否同城',
        ];
    }

    /**
     * 更新route
     * @Author:Fenghuan
     * @param $data
     * @param $condition
     * @return int
     */
    public function updateRoute($data, $condition)
    {
        return self::updateAll($data, $condition);
    }

    //route->car
    public function getRouteOfCar()
    {
        //tableName of joinWith is lower case
        return $this->hasMany(LogisticsCar::className(), ['logistics_route_id' => 'logistics_route_id'])->joinWith('carOfDriver');
    }

    //route -> logistics_area
    public function getRouteOfLogisticsArea()
    {
        return $this->hasOne(LogisticsArea::className(), ['logistics_route_id' => 'logistics_route_id']);
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
//             $condition['area_id'] = 0;
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
     * 判断拼音状态
     * @Author:Fenghuan
     * @param $id
     * @return bool
     */
    public function getPinyinStatus($id)
    {
        $item = $this->getLogisticsRouteFindOne($id);

        if ($item->area_id) {
            $res = Area::findModel($item->area_id);
        }
        else if ($item->city_id) {
            $res = Area::findModel($item->city_id);
        }
        else{
            //异常
            return false;
        }

        return $res;


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
     * 获取司机列表
     * @param unknown $logRouteId
     * @return \common\models\unknown|array|\yii\db\ActiveRecord[]
     */
    public function driverlist($logRouteId = null)
    {
        $arr['user'] = array();
        $arr['LogisticsRoute'] = $this->getLogRoute();
        if($logRouteId > 0 )
        {
            $arr['user'] = $this->getlogisticsRouteUser($logRouteId);
            if(!empty($arr['user']))
            {
                foreach ($arr['user'] as $k => $v)
                {
                    $arr['user'][$k]['App_Key'] = UserAll::findOne($v['id'])->App_Key;
                }
            }
        }
        return $arr;
    }
    
    /**
     * 获取线路信息
     * @return unknown
     */
    private function getLogRoute()
    {
        $model = new LogisticsRoute();
        $arr['sameCity'] = array();
        $arr['sameCity2'] = array();
        $arr['sameCity'] = $model->getLogisticsRouteInfo(array('same_city'=>1));
        $arr['sameCity2'] = $model->getLogisticsRouteInfo(array('same_city'=>2));
        return $arr;
    }
    
    /**
     * 获取线路对应的用户
     */
    private function getlogisticsRouteUser($logRouteId)
    {
        $query = LogisticsRoute::find();
        $query->select('user.username,user.user_truename,user.id');
        $query->innerJoin('logistics_car','logistics_route.logistics_route_id = logistics_car.logistics_route_id');
        $query->innerJoin('driver','driver.logistics_car_id = logistics_car.logistics_car_id');
        $query->innerJoin('user','driver.member_id = user.id');
        $query->where(['logistics_route.logistics_route_id'=>1]);
        return $query->asArray()->all();
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
