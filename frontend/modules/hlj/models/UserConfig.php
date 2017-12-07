<?php

namespace frontend\modules\hlj\models;

use Yii;

/**
 * This is the model class for table "user_config".
 *
 * @property int $user_id
 * @property int $employee_print 是否自动打印
 */
class UserConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_config';
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
            [['user_id'], 'required'],
            [['user_id', 'employee_print'], 'integer'],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'employee_print' => 'Employee Print',
        ];
    }
    
    /**
     * 开单是否自动打印
     */
    public function isPrint(){
        $model = $this::findOne(Yii::$app->user->id);
        if(empty($model)){
            $this->user_id = Yii::$app->user->id;
            $this->employee_print = 1;
            $this->save();
            return $this->employee_print;
        }else{
            return $model->employee_print;
        }
    }
    
    /**
     * 自动打印状态修改
     */
    public function editPrint(){
        $result = array();
        $model = $this::findOne(Yii::$app->user->id);
        if($model->employee_print == 0){
            $model->employee_print = 1;
            $result['status'] = 1;
        }else if($model->employee_print == 1){
            $model->employee_print = 0;
            $result['status'] = 0;
        }
        $result['boolean'] = $model->save();
        return $result;
    }
}
