<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_member_type".
 *
 * @property int $member_id 会员id
 * @property int $member_type 会员类型(默认0;1:修配厂)
 */
class MemberType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_member_type';
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
            [['member_id'], 'required'],
            [['member_id', 'member_type'], 'integer'],
            [['member_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'member_type' => 'Member Type',
        ];
    }
    
    public function addMemberType($memberId)
    {
    	$this->member_id= $memberId;
    	$this->member_type= 1;//修理厂
    	$this->save();
    }
}
