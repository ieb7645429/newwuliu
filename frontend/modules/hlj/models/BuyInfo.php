<?php

namespace frontend\modules\hlj\models;

use Yii;
use common\yjmodels\Member;
use common\yjmodels\MemberPoints;
use common\yjmodels\MemberPointsLog;
use common\yjmodels\MemberTotal;
use common\yjmodels\MemberType;

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
            [['phone', 'province_id', 'city_id', 'area_id'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['area_info'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'user_id' => 'User ID',
            'name' => 'Name',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'area_id' => 'Area ID',
            'area_info' => 'Area Info',
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
        //$this->addYoujianMember($data);
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
    		$memberId = $yjModelMember->addMember($data);
    		$yjModelMemberPoints->addMemberPoints($memberId);
    		$yjModelMemberPointsLog->addMemberPointsLog($memberId);
    		$yjModelMemberTotal->addMemberTotal($memberId);
    		$yjModelMemberType->addMemberType($memberId);
    	}
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
