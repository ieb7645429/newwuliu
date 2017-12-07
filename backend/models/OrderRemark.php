<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_remark".
 *
 * @property int $order_id 订单Id
 * @property string $content 注释内容
 * @property int $user_id 用户ID
 * @property string $add_time 添加时间
 */
class OrderRemark extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_remark';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'user_id'], 'integer'],
            [['add_time','edit_time'], 'safe'],
            [['content'], 'string', 'max' => 255],
            [['edit_content'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'content' => 'Content',
            'user_id' => 'User ID',
            'add_time' => 'Add Time',
            'edit_content' => '备注',
        ];
    }
    
    /**
     * 添加记录
     * @param unknown $params
     * @return boolean
     */
    public function addRemark($params) {
        $this->order_id = intval($params['order_id']);
        $this->content = $params['content'];
        $this->user_id = Yii::$app->user->id;
        return $this->save();
    }
    
    /**
     * 落地点添加记录
     * @param unknown $params
     * @return boolean
     */
    public function addTerminusRemark($params) {
        $this->order_id = intval($params['order_id']);
        $this->terminus_content = $params['content'];
        $this->terminus_id = $params['terminus_id'];
        $this->terminus_add_time = time();
        return $this->save();
    }
    
    /**
     * 添加修改记录
     * @param unknown $params
     * @return boolean
     */
    public function addEditRemark($params) {
        $model = $this::findOne($params['order_id']);
        if(empty($model)){
            $this->order_id = intval($params['order_id']);
            $this->edit_content = htmlspecialchars($params['edit_content']);
            $this->edit_time = time();
            $this->edit_user_id = Yii::$app->user->id;
            return $this->save();
        }else{
            $model->order_id = intval($params['order_id']);
            $model->edit_content = htmlspecialchars($params['edit_content']);
            $model->edit_time = time();
            $model->edit_user_id = Yii::$app->user->id;
            return $model->save();
        }
        
    }
}
