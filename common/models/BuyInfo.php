<?php

namespace common\models;

use Yii;
use common\yjmodels\Member;
use common\yjmodels\MemberPoints;
use common\yjmodels\MemberPointsLog;
use common\yjmodels\MemberTotal;
use common\yjmodels\MemberType;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "buy_info".
 *
 * @property int $id
 * @property string $phone 电话
 * @property int $user_id user_id
 * @property string $name 收货人姓名
 * @property int $province_id 省id
 * @property int $city_id 市id
 * @property int $area_id 区id
 * @property string $area_info 收货详细地址
 *@property int $logistics_route_id 线路id 
* @property int $terminus_id 落地点id 
* @property int $is_receive    是否可以收货0为可以，1为不可以  
 */
class BuyInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'buy_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'name', 'province_id', 'city_id', 'area_id'], 'required'],
            [['phone', 'province_id', 'city_id', 'area_id', 'logistics_route_id', 'terminus_id', 'is_receive'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['area_info'], 'string', 'max' => 255],
            [['phone'], 'unique'], 
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => '收货人电话',
            'user_id' => 'User ID',
            'name' => '收货人名',
            'province_id' => '收货人省',
            'city_id' => '收货人市',
            'area_id' => '收货人区',
            'area_info' => '收货人详细地址',
            'logistics_route_id' => '线路',
            'terminus_id' => '落地点',
            'is_receive' => '是否加入黑名单', 
        ];
    }

    /**
     * 朱鹏飞
     * 增加收货人信息
     * @param unknown $data
     */
    public function addBuyInfo($data){
        $buyInfo = self::findOne(['phone'=>$data->receiving_phone]);
        if(empty($buyInfo))
        {
        	$buyInfo = new BuyInfo();
        }
        $buyInfo->phone = $data->receiving_phone;
        $buyInfo->name = $data->receiving_name;
        $buyInfo->province_id = $data->receiving_provinceid;
        $buyInfo->city_id = $data->receiving_cityid;
        $buyInfo->area_id = $data->receiving_areaid;
        $buyInfo->area_info = $data->receiving_name_area;
        $buyInfo->logistics_route_id = $data->logistics_route_id;
        $buyInfo->terminus_id = $data->terminus_id?$data->terminus_id:0;
        $buyInfo->save();
        $this->addYoujianMember($data);
    }
    
    /**
     * 增加友件网用户信息
     * @param array $data
     */
    public function addYoujianMember($data)
    {
    	$yjModelMember = new Member();
    	$yjModelMemberPoints = new MemberPoints();
    	$yjModelMemberPointsLog = new MemberPointsLog();
    	$yjModelMemberTotal = new MemberTotal();
    	$yjModelMemberType = new MemberType();
    	$yjMemberInfo = $yjModelMember->getMemberInfo($data['receiving_phone']);
    	if($yjMemberInfo  === false)
    	{
    	    $data = ArrayHelper::toArray($data);
    	    $data['area'] = 'sy';
    		$memberId = $yjModelMember->addMember($data);
    		$yjModelMemberPoints->addMemberPoints($memberId);
    		$yjModelMemberPointsLog->addMemberPointsLog($memberId);
    		$yjModelMemberTotal->addMemberTotal($memberId);
    		$yjModelMemberType->addMemberType($memberId);
    	}
    }
    
    /**
     * 获取落地点信息
     * @param unknown $terminusId
     * @return \common\models\Terminus
     */
    public function getTerminusName($terminusId)
    {
        return Terminus::findOne($terminusId);
    }
    
    /**
     * 获取物流线路
     * @param unknown $logisticsRouteId
     * @return \common\models\LogisticsRoute
     */
    public function getLogisticsRoute($logisticsRouteId)
    {
        return LogisticsRoute::findOne($logisticsRouteId);
    }
    
    /**
     * 获取城市信息
     * @param unknown $id
     * @return \common\models\Area
     */
    public function getAreaInfo($id)
    {
        return Area::findOne($id);
    }
    
    /**
     * @desc 取得收货人信息
     * @author 暴闯
     * @return array 收货人信息
     */
    public function getBuyInfo($phone)
    {
        return self::find()->where('phone = :phone', [':phone' => $phone])->asArray()->one();
    }
	 /**
     * @desc 取得收货人信息
     * @author 小雨
     * @return array 收货人信息,增加判断是否可以收货
     */
    public function getBuyInfo_new($phone,$status=0)
    {
        return self::find()->where('phone = :phone', [':phone' => $phone])
			   ->andwhere('is_receive = :status', [':status' => $status])->asArray()->one();
    }
}
