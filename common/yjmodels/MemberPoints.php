<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_member_points".
 *
 * @property string $uid 用户ID
 * @property int $points 积分
 * @property string $update_time 更新时间
 */
class MemberPoints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_member_points';
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
            [['uid', 'points'], 'required'],
            [['uid', 'points'], 'integer'],
            [['update_time'], 'safe'],
            [['uid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'points' => 'Points',
            'update_time' => 'Update Time',
        ];
    }
    
    public function addMemberPoints($memberId)
    {
    	$this->uid = $memberId;
    	$this->points = 0;
    	$this->update_time = date('Y-m-d H:i:s', time());
    	$this->save();
    }
}
