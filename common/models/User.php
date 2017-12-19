<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use frontend\models\SignupForm;
use common\yjmodels\Seller;
use common\yjmodels\Store;
use common\yjmodels\Member;
use yii\db\Exception;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           // ['username','required']

        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function attributeLabels()
    {
        return [
                'small_name' => '简名',
                'username' => '会员号',
			    'small_num' => '会员小号',
        ];
    }

    /**
     * 返回注册用户主键
     * @Author:Fenghuan
     * @param $model
     * @return $user
     * @throws Exception
     */
    public function createUser($model)
    {
//        $model = new CreateUserForm();
        $userInfo = User::findByUsername($model->username);
        if ($userInfo) {
            throw new Exception('用户已存在');

        } else {
            if (!$user = $model->signup()) {
                throw new Exception('用户注册失败');
            }

            return $user;
        }

    }

//    public function getUserJoinDriver()
//    {
//        return $this->hasOne(Driver::className(), ['member_id' => 'id']);
//    }

    
    /**
     * 判断是否买断
     * 朱鹏飞
     * @param unknown $member_phone
     * @return 1 买断
     * @return 2 不买断
     */
    public function getMemberCollection($is_buy_out = ''){
        if(!empty($member_info)){
            if($is_buy_out == 1){
                return 1;
            }
        }
        return 2;
    }
    
    /**
     * 获取用户信息
     * @param unknown $phone
     */
    public function getMemberInfo($condition){
        return self::find()->where($condition)->one();
    }
    
	public function getMemberInfoAll($condition){
        return self::find()->where($condition)->select('user_truename')->asArray()->all();
    }
    /**
     * 获取用户信息，并注册成用户
     * @param unknown $data
     */
    public function userInfo($data, $username) {
        $modelArea = new Area();
        $areaInfo = $modelArea->getAreaInfo(array('area_parent_id'=>$data['member_cityid']));
        $model = new SignupForm();
        $model->username = $username;
        $model->password = '123456';
        $model->status = '10';
        $model->created_at = time();
        $model->updated_at = time();
        $model->user_truename = $data['member_name'];
        $model->member_phone = $data['member_phone'];
        $model->member_cityid=$data['member_cityid'];
        $model->member_provinceid= 6;
        $model->member_areaid= '0';
        $model->member_areainfo ='0';
        return $model->signup();//新用户注册，默认都是是买断
    }
    /**
	*  扫码枪端登陆验证
	*  2017/07/21
	*  增加type值,type不为空说明是app司机登陆,为空扫码枪登录
	*  2017/10/10
	*  rain
	**/
	public function Check_App_Login($data,$type=''){
	   $token = '';
	   $flag  = '';
       $value = self::findByUsername($data['user']);
	   $this->password_hash = $value->password_hash;
	   //2017-11-15，扫码登陆不需要验证密码
       if(empty($type)){
	     $pwd = true;
	   }
	   else{
	     $pwd = $this->validatePassword($data['pwd']);
	   }
	   if($pwd)
	   {
		 $this->generateAuthKey();//获取tonken
		 $token = $this->auth_key;

		 if(!empty($type)){
			$login = new AppLogin();
            $login_ = $login->findOne(['user_id' =>$value->id]);
			if(!empty($login_)){
			  $login = $login_;
			}
			$login->user_id = $value->id;
			//判断status是否为1，若为1不记录token
			if($login->status==0){
			  $login->token   = $token;
			  /*2017-10-10修改*/
			  if($login->save()){
			   $flag = $login->token;
			  }

			}
			//status为1不允许登陆、2017-10-10
			elseif($login->status==1){
			  $flag = 'no';
			}
            /* if($login->save()){
			   $flag = $login->token;
			  }
			*/
		 }
		 else{
			 $value->App_Key = $token;
			 if($value->save()){
			   $flag = $token;
			 }
		 }
	   }
	   return  $flag;
	}
	/**
	*  查询用户相关信息
	*  @参数 $data
	*  2017/09/01
	*  rain
	**/
    public function getsearch($data){
       $sql = '';
	   $datas = array();
	   if($data['contacts_phone'] == '' && $data['store_name'] == '' && $data['small_num'] == '' && $data['province_id'] == ''){
          return '';
		  exit;
        }
        $contacts_phone = $data['contacts_phone'];
        $store_name     = $data['store_name'];
		$small_num      = $data['small_num'];
		$province_id    = $data['province_id'];
      /* if($contacts_phone!='' && $store_name!='' && $small_num !=''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where usernmae='$contacts_phone' and 	user_truename like '%$store_name%' and member_provinceid='$province_id'";
        }else if ($contacts_phone!='' && $store_name=='' && $small_num ==''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where username='$contacts_phone' and member_provinceid='$province_id'";
        }else if($contacts_phone == '' && $store_name !='' && $small_num ==''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where user_truename like '%$store_name%' and member_provinceid='$province_id'";
        }
		else if($contacts_phone == '' && $store_name =='' && $small_num !=''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where small_num='$small_num' and member_provinceid='$province_id'";
        }*/
if($contacts_phone!='' && $store_name!='' && $small_num !=''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where usernmae='$contacts_phone' and 	user_truename like '%$store_name%' and member_provinceid='$province_id'";
        }else if ($contacts_phone!='' && $store_name=='' && $small_num ==''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where username='$contacts_phone' and member_provinceid='$province_id'";
        }else if($contacts_phone == '' && $store_name !='' && $small_num ==''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where user_truename like '%$store_name%' and member_provinceid='$province_id'";
        }
		else if($contacts_phone == '' && $store_name =='' && $small_num !=''){
			$sql = "select small_num,username as member_name ,user_truename as store_name from user where small_num='$small_num' and member_provinceid='$province_id'";
        }
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
	
	/**
	 * 判断用户名是否重复
	 */
	public function issetUserName($username){
	    $member = new Member();
        $seller = new Seller();
        $store = new Store();
	    
        $count = $this::find()->where(['username'=>$username])->count();
	    $count_youjian_store = $store::find()->where(['member_name'=>$username])->count();
	    $count_youjian_seller = $seller::find()->where(['seller_name'=>$username])->count();
	    $count_youjian_member = $member::find()->where(['member_name'=>$username])->count();
	    if($count>0||$count_youjian_store>0||$count_youjian_seller>0||$count_youjian_member>0)
	        return false;
	    return true;
	}
	/**
	 * 验证会员号是否为手机号
	 * @param unknown $param
	 */
	public function phoneFormat($param){
	    return preg_match('/^[1][34578][0-9]{9}$/',$param['username']);
	}

    /**
     * @desc 根据Id取得user_trueName
     * @param unknown $id
     * @return string
     */
    public static function getUserNameById($id) {
        $area = static::findOne($id);
        if($area) {
            return $area->user_truename;
        }
        return '';
    }

    /**
     * @desc 根据Id取得userName
     * @param unknown $id
     * @return string
     */
    public static function getUserNameById1($id) {
        $area = static::findOne($id);
        if($area) {
            return $area->username;
        }
        return '';
    }

}
