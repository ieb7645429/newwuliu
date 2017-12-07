<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;

/**
* @property int $id
* @property string $username
* @property string $auth_key
* @property string $password_hash
* @property string $password_reset_token
* @property string $email
* @property int $status
* @property int $created_at
* @property int $updated_at
* @property string $user_truename 真实姓名
* @property int $is_poundage 代收的手续费（1收,2不收）
* @property int $is_buy_out 买断(1买断,2不买断)
* @property int $buy_out_price 买断百分比（50，买断50%）
* @property int $buy_out_time 买断时间(24, 买断24小时)
* @property string $member_phone 电话
* @property int $member_areaid 区id
* @property int $member_cityid 市id
* @property int $member_provinceid 省id
* @property string $member_areainfo 详细地址
*/
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $user_truename;
    public $member_phone;
    public $member_areaid;
    public $member_cityid;
    public $member_provinceid;
    public $member_areainfo;
    public $province;
    public $city;
    public $district;
    public $status;
    public $created_at;
    public $updated_at;
    public $buy_out_price;
    public $buy_out_time;

    /**
     * @inheritdoc
     */
    
    public static function tableName()
    {
        return 'user';
    }
    
    
    public function rules()
    {
        return [
            [['username','password','member_phone','user_truename', 'member_provinceid', 'member_cityid', 'member_areaid'],'required'],
            ['username', 'trim'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '用户名已经存在。'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'string', 'min' => 6],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => '邮箱已经存在。'],

            ['member_phone', 'trim'],
            ['member_phone', 'required'],
            ['member_phone', 'integer'],
            
            ['member_areainfo', 'trim']
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->email = $this->email;
        $user->member_phone = $this->member_phone;
        $user->user_truename = $this->user_truename;
        $user->member_areaid = $this->member_areaid;
        $user->member_cityid = $this->member_cityid;
        $user->member_provinceid = $this->member_provinceid;
        $user->member_areainfo = $this->member_areainfo;
        $user->generateAuthKey();
        return $user->save() ? $user : null;
    }
    
    public function attributeLabels(){
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'member_phone'=>'电话',
            'user_truename'=>'真实姓名',
            'is_poundage'=>'代收手续费',
            'is_buy_out'=>'买断',
            'buy_out_price'=>'买断百分比',
            'buy_out_time'=>'买断时间',
            'district'=>'省/市/区',
            'member_areainfo'=>'详细地址',
        ];
    }
    
}
