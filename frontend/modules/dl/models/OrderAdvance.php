<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "order_advance".
 *
 * @property int $id
 * @property int $order_id
 * @property string $amount 垫付金额
 * @property string $logistics_sn 票号
 * @property int $state 状态（1已收款，2未收款）
 * @property string $add_time 添加时间
 * @property int $add_user 添加用户Id
 * @property string $income_time 收款时间
 * @property int $income_user 收款用户Id
 */
class OrderAdvance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_advance';
    }
    
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_dl');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'logistics_sn', 'add_time', 'add_user'], 'required'],
            [['order_id', 'state', 'add_time', 'add_user', 'income_time', 'income_user'], 'integer'],
            [['amount'], 'number'],
            [['logistics_sn'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'amount' => 'Amount',
            'logistics_sn' => 'Logistics Sn',
            'state' => 'State',
            'add_time' => 'Add Time',
            'add_user' => 'Add User',
            'income_time' => 'Income Time',
            'income_user' => 'Income User',
        ];
    }
}
