<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_member".
 *
 * @property int $member_id 会员id
 * @property string $member_name 会员名称
 * @property string $member_truename 真实姓名
 * @property string $member_avatar 会员头像
 * @property int $member_sex 会员性别
 * @property string $member_birthday 生日
 * @property string $member_passwd 会员密码
 * @property string $member_paypwd 支付密码
 * @property string $member_email 会员邮箱
 * @property int $member_email_bind 0未绑定1已绑定
 * @property string $member_mobile 手机号
 * @property int $member_mobile_bind 0未绑定1已绑定
 * @property string $member_qq qq
 * @property string $member_ww 阿里旺旺
 * @property int $member_login_num 登录次数
 * @property string $member_time 会员注册时间
 * @property string $member_login_time 当前登录时间
 * @property string $member_old_login_time 上次登录时间
 * @property string $member_login_ip 当前登录ip
 * @property string $member_old_login_ip 上次登录ip
 * @property string $member_qqopenid qq互联id
 * @property string $member_qqinfo qq账号相关信息
 * @property string $member_sinaopenid 新浪微博登录id
 * @property string $member_sinainfo 新浪账号相关信息序列化值
 * @property string $weixin_unionid 微信用户统一标识
 * @property string $weixin_info 微信用户相关信息
 * @property int $member_points 会员积分
 * @property string $available_predeposit 预存款可用金额
 * @property string $freeze_predeposit 预存款冻结金额
 * @property string $available_rc_balance 可用充值卡余额
 * @property string $freeze_rc_balance 冻结充值卡余额
 * @property int $inform_allow 是否允许举报(1可以/2不可以)
 * @property int $is_buy 会员是否有购买权限 1为开启 0为关闭
 * @property int $is_allowtalk 会员是否有咨询和发送站内信的权限 1为开启 0为关闭
 * @property int $member_state 会员的开启状态 1为开启 0为关闭
 * @property int $member_snsvisitnum sns空间访问次数
 * @property int $member_areaid 地区ID
 * @property int $member_cityid 城市ID
 * @property int $member_provinceid 省份ID
 * @property string $member_areainfo 地区内容
 * @property string $member_privacy 隐私设定
 * @property string $member_quicklink 会员常用操作
 * @property int $member_exppoints 会员经验值
 * @property int $inviter_id 邀请人ID
 * @property string $member_wxopenid 微信互联openid
 * @property string $car_repair_factory 汽配修理厂
 * @property string $buy_code 电话采购码
 * @property int $is_insurance 是否为保险用户
 * @property int $is_union 是否为区域合作商
 * @property string $shop_image
 * @property string $registration_id 极光推送的注册ID
 * @property string $inviter_code 邀请人的邀请码
 */
class Member extends \yii\db\ActiveRecord
{
    const SCENARIO_EDIT = 'edit';
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EDIT] = [];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_member';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_name', 'member_passwd', 'member_email', 'member_time', 'member_login_time', 'member_old_login_time', 'car_repair_factory', 'registration_id', 'inviter_code'], 'required'],
            [['member_sex', 'member_email_bind', 'member_mobile_bind', 'member_login_num', 'member_points', 'inform_allow', 'is_buy', 'is_allowtalk', 'member_state', 'member_snsvisitnum', 'member_areaid', 'member_cityid', 'member_provinceid', 'member_exppoints', 'inviter_id', 'is_insurance', 'is_union'], 'integer'],
            [['member_birthday'], 'safe'],
            [['member_qqinfo', 'member_sinainfo', 'member_privacy'], 'string'],
            [['available_predeposit', 'freeze_predeposit', 'available_rc_balance', 'freeze_rc_balance'], 'number'],
            [['member_name', 'member_avatar', 'weixin_unionid'], 'string', 'max' => 50],
            [['member_truename', 'member_login_ip', 'member_old_login_ip'], 'string', 'max' => 20],
            [['member_passwd', 'member_paypwd'], 'string', 'max' => 32],
            [['member_email', 'member_qq', 'member_ww', 'member_qqopenid', 'member_sinaopenid', 'member_wxopenid'], 'string', 'max' => 100],
            [['member_mobile'], 'string', 'max' => 11],
            [['member_time', 'member_login_time', 'member_old_login_time'], 'string', 'max' => 10],
            [['weixin_info', 'member_areainfo', 'member_quicklink', 'shop_image', 'registration_id', 'inviter_code'], 'string', 'max' => 255],
            [['car_repair_factory'], 'string', 'max' => 40],
            [['buy_code'], 'string', 'max' => 80],
            [['member_name'], 'unique'],
            [['buy_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
            'member_truename' => 'Member Truename',
            'member_avatar' => 'Member Avatar',
            'member_sex' => 'Member Sex',
            'member_birthday' => 'Member Birthday',
            'member_passwd' => 'Member Passwd',
            'member_paypwd' => 'Member Paypwd',
            'member_email' => 'Member Email',
            'member_email_bind' => 'Member Email Bind',
            'member_mobile' => 'Member Mobile',
            'member_mobile_bind' => 'Member Mobile Bind',
            'member_qq' => 'Member Qq',
            'member_ww' => 'Member Ww',
            'member_login_num' => 'Member Login Num',
            'member_time' => 'Member Time',
            'member_login_time' => 'Member Login Time',
            'member_old_login_time' => 'Member Old Login Time',
            'member_login_ip' => 'Member Login Ip',
            'member_old_login_ip' => 'Member Old Login Ip',
            'member_qqopenid' => 'Member Qqopenid',
            'member_qqinfo' => 'Member Qqinfo',
            'member_sinaopenid' => 'Member Sinaopenid',
            'member_sinainfo' => 'Member Sinainfo',
            'weixin_unionid' => 'Weixin Unionid',
            'weixin_info' => 'Weixin Info',
            'member_points' => 'Member Points',
            'available_predeposit' => 'Available Predeposit',
            'freeze_predeposit' => 'Freeze Predeposit',
            'available_rc_balance' => 'Available Rc Balance',
            'freeze_rc_balance' => 'Freeze Rc Balance',
            'inform_allow' => 'Inform Allow',
            'is_buy' => 'Is Buy',
            'is_allowtalk' => 'Is Allowtalk',
            'member_state' => 'Member State',
            'member_snsvisitnum' => 'Member Snsvisitnum',
            'member_areaid' => 'Member Areaid',
            'member_cityid' => 'Member Cityid',
            'member_provinceid' => 'Member Provinceid',
            'member_areainfo' => 'Member Areainfo',
            'member_privacy' => 'Member Privacy',
            'member_quicklink' => 'Member Quicklink',
            'member_exppoints' => 'Member Exppoints',
            'inviter_id' => 'Inviter ID',
            'member_wxopenid' => 'Member Wxopenid',
            'car_repair_factory' => 'Car Repair Factory',
            'buy_code' => 'Buy Code',
            'is_insurance' => 'Is Insurance',
            'is_union' => 'Is Union',
            'shop_image' => 'Shop Image',
            'registration_id' => 'Registration ID',
            'inviter_code' => 'Inviter Code',
        ];
    }
    
    /**
     * 获取友件网用户信息
     */
    public function getMemberInfo($memberName)
    {
    	$info = $this->find()->where(['member_name'=>$memberName])->one();
    	if($info)
    	{
    		return true;
    	}else{
    		return false;
    	}
    }
    
    
    /**
     * 增加友件买家帐号
     * @param unknown $param
     */
    public function addMember($param)
    {
    	$model = new Member();
    	$model->member_name = $param['receiving_phone'];
    	$model->member_truename = $param['receiving_name'];
    	$model->member_passwd = md5(123456);
    	$model->member_email = '0';
    	$model->member_mobile = $param['receiving_phone'];
    	$model->member_time = "time()";
    	$model->member_login_time = "time()";
    	$model->member_old_login_time = "time()";
    	$model->member_login_ip = $_SERVER["REMOTE_ADDR"];
    	$model->member_old_login_ip = $_SERVER["REMOTE_ADDR"];
    	$model->member_cityid = $param['receiving_cityid'];
    	$model->member_provinceid = $param['receiving_provinceid'];
    	$model->member_areaid = $param['receiving_areaid'];
    	$model->member_areainfo = $param['receiving_name_area'];
    	$model->car_repair_factory = "0";
    	$model->registration_id = "0";
    	$model->inviter_code = "0";
    	$model->area = $param['area'];
    	$model->save();
    	return $model->attributes['member_id'];
    }
    
    /**
     * 靳健
     * 物流会员账号修改,同时更改友件member信息
     * @param unknown $model
     */
    public function memberInfoEdit($model){
        $member_model = $this::findOne(['member_name'=>$model->getOldAttribute('username')]);
        if(!empty($member_model)){
            $member_model->scenario = 'edit';
            $member_model->member_name = $model->username;
            return $member_model->save();
        }
        return true;
    }
}
