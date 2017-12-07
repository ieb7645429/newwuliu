<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "driver_config".
 *
 * @property int $user_id
 * @property int $small_print_status 小码单是否全部打印（0全部，1部分）
 * @property int $driver_manager_status 司机领队选中默认司机id
 */
class DriverConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'driver_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['small_print_status', 'driver_manager_status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'small_print_status' => 'Small Print Status',
            'driver_manager_status' => 'Driver Manager Status',
        ];
    }
    
    /**
     * 获取小码单打印状态
     */
    public function getSmallPrintStatus(){
        $model = $this::findOne(Yii::$app->user->id);
        if($model){
            return $model->small_print_status;
        }else{
            $new = new self();
            $new->user_id = Yii::$app->user->id;
            $new->small_print_status = 0;
            $new->save();
            return $new->small_print_status;
        }
    }
    
    /**
     * 修改小码单是否全部打印
     */
    public function editSmallPrint(){
        $result = array();
        $model = $this::findOne(Yii::$app->user->id);
        if($model->small_print_status == 0){
            $model->small_print_status = 1;
            $result['status'] = 1;
        }else if($model->small_print_status == 1){
            $model->small_print_status = 0;
            $result['status'] = 0;
        }
        $result['boolean'] = $model->save();
        return $result;
    }
    
    /**
     * 获取司机领队选中司机
     * @return number
     */
    public function getDriverManagerStatus(){
        $model = $this::findOne(Yii::$app->user->id);
        if($model){
            return $model->driver_manager_status;
        }else{
            $new = new self();
            $new->user_id = Yii::$app->user->id;
            $new->driver_manager_status = 0;//默认无选中司机
            $new->save();
            return $new->driver_manager_status;
        }
    }
    
    /**
     * 改变司机领队选中司机
     * @param unknown $driver_id
     */
    public function editDriverManagerStatus($driver_id){
        $model = $this::findOne(Yii::$app->user->id);
        $model->driver_manager_status = $driver_id;
        return $model->save();
    }
}
