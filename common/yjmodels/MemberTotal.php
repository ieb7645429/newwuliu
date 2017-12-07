<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_member_total".
 *
 * @property string $id
 * @property string $uid 用户ID
 * @property string $begin_time 统计开始时间
 * @property string $end_time 统计结束时间
 * @property string $total_amount 总金额
 */
class MemberTotal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_member_total';
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
            [['uid', 'begin_time', 'end_time'], 'required'],
            [['uid', 'begin_time', 'end_time'], 'integer'],
            [['total_amount'], 'number'],
            [['uid'], 'unique'],
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
            'begin_time' => 'Begin Time',
            'end_time' => 'End Time',
            'total_amount' => 'Total Amount',
        ];
    }
    
    public function addMemberTotal($memberId)
    {
    	$time = time();
    	$this->uid = $memberId;
    	$this->begin_time = $time;
    	$this->end_time = $time;
    	$this->total_amount = 0;
    	$this->save();
    }
}
