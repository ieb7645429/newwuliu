<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "app_login".
 *
 * @property int $app_login_id
 * @property int $user_id
 * @property int $token
 * @property int $status 状态值为0和1
 */
class AppLogin extends \yii\db\ActiveRecord
{
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['edit'] = [];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'token'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['token'],'string', 'max' => 255],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'app_login_id' => 'App Login ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'status' => 'Status',
        ];
    }
	/**
	*  查询
	**/
	public function SearchInfo($arr){
	    return self::findOne($arr);
	}
	/**
	* 改变状态
	* 入参 $where,$value
	**/
	public function ChangeStatus($where,$value){
	   $state = self::findOne($where);
	   if(!empty($state)){
			$state->status = $value;
			return $state->save();
	   }
	}
	
	/**
	 * 封车时更新状态
	 */
	public function updateStateOne(){
	    //大司机封车获取司机id
	    $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
	    if($role==Yii::$app->params['roleDriverManagerCityWide']||$role==Yii::$app->params['roleDriverManager']){
	        $driver_member_id = $goods::find()->where(['and','order_id ='.$value,['<>','driver_member_id',0]])->asArray()->all()[0]['driver_member_id'];
	    }else{
	        $driver_member_id = Yii::$app->user->id;
	    }
	    
	    $model = $this::find()->where(['user_id'=>$driver_member_id,'status'=>1])->one();
	    if(!empty($model)){
	        $logisticsLines = new LogisticsLines();
	        $lineModel = $logisticsLines->find()->where(['driver_member_id'=>$driver_member_id,'state'=>2])->all();
	        if(!empty($lineModel)){//更新line表
	            foreach($lineModel as $key => $value){
	                $line = $logisticsLines::findOne($value['order_id']);
	                $line->state = 1;
	                $line->save();
	            }
	        }
	        $model->setScenario('edit');
	        $model->status = 0;
	        $model->save();
	    }
	}
}
