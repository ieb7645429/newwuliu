<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use common\models\UserAll;

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
class CreateUserForm extends Model
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
    public $small_num;

    //司机情况下不验证数据规则
    const SCENARIO_DRIVER = 'driver';
//
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DRIVER] = ['username', 'email','password','member_phone','user_truename', 'member_provinceid', 'member_cityid'];
        return $scenarios;
    }

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
            [['username','password','member_phone','user_truename', 'member_provinceid', 'member_cityid', 'member_areaid', 'small_num'],'required'],
            ['username', 'trim'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '用户名已经存在。'],
//             ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'string', 'min' => 6],

            ['member_phone', 'trim'],
            ['member_phone', 'required'],
            ['member_phone', 'integer'],
            ['member_phone', 'match', 'pattern'=>'/^[1][34578][0-9]{9}$/', 'message' => '请输入正确手机号'],
            
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


        $userAll = new UserAll();
        $userAll->username = $this->username;
        $userAll->setPassword($this->password);
        $userAll->generateAuthKey();
        $userAll->area = 'sy';

        if(!$userAll->save()) {
            return false;
        }

        $user = new User();
        $user->id = $userAll->id;
        $user->username = $this->username;
        $user->member_phone = $this->member_phone;
        $user->user_truename = $this->user_truename;
        $user->member_areaid = isset($this->member_areaid) ? $this->member_areaid : 0;
        $user->member_cityid = $this->member_cityid;
        $user->member_provinceid = $this->member_provinceid;
        $user->member_areainfo = $this->member_areainfo;
        $user->small_num = $this->small_num;
        return $user->save() ? $user : $user->errors;
    }

    /**
     * 注册黑龙江
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup1()
    {
        if (!$this->validate()) {
            return null;
        }


        $userAll = new UserAll();
        $userAll->username = $this->username;
        $userAll->setPassword($this->password);
        $userAll->generateAuthKey();
        $userAll->area = 'hlj';

        if(!$userAll->save()) {
            return false;
        }

        $user = new \frontend\modules\hlj\models\User();
        $user->id = $userAll->id;
        $user->username = $this->username;
        $user->member_phone = $this->member_phone;
        $user->user_truename = $this->user_truename;
        $user->member_areaid = isset($this->member_areaid) ? $this->member_areaid : 0;
        $user->member_cityid = $this->member_cityid;
        $user->member_provinceid = $this->member_provinceid;
        $user->member_areainfo = $this->member_areainfo;
        $user->small_num = $this->small_num;
        return $user->save() ? $user : $user->errors;
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
