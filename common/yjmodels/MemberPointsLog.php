<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_member_points_log".
 *
 * @property string $id
 * @property string $uid 用户id
 * @property int $points 积分
 * @property int $before_point 前积分
 * @property int $after_point 后积分
 * @property string $content 内容
 * @property int $type 类型（1收入，2支出）
 * @property int $source_type 类型（1注册，2购物，3抽奖，4退款，5中奖）
 * @property string $add_time 添加时间
 */
class MemberPointsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_member_points_log';
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
            [['uid', 'points', 'content', 'type', 'source_type', 'add_time'], 'required'],
            [['uid', 'points', 'before_point', 'after_point', 'type', 'source_type', 'add_time'], 'integer'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'points' => 'Points',
            'before_point' => 'Before Point',
            'after_point' => 'After Point',
            'content' => 'Content',
            'type' => 'Type',
            'source_type' => 'Source Type',
            'add_time' => 'Add Time',
        ];
    }
    
    public function addMemberPointsLog($memberId)
    {
    	$this->uid = $memberId;
    	$this->points = 0;
    	$this->before_point = 0;
    	$this->after_point = 0;
    	$this->content = '注册赠送0友豆';
    	$this->type = 1;
    	$this->source_type = 1;
    	$this->add_time = time();
    	$this->save();
    }
}
