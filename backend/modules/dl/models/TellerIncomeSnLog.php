<?php

namespace backend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "teller_income_sn_log".
 *
 * @property int $id
 * @property string $order_sn 订单id
 * @property int $count 票数
 * @property string $amount 金额
 * @property int $user_id 收款人Id
 * @property string $receiving 收款对象
 * @property string $add_time 收款时间
 */
class TellerIncomeSnLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teller_income_sn_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'count', 'amount', 'user_id', 'receiving', 'add_time'], 'required'],
            [['count', 'user_id', 'add_time'], 'integer'],
            [['amount'], 'number'],
            [['order_sn'], 'string', 'max' => 255],
            [['receiving'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => '票号',
            'count' => '票数',
            'amount' => '金额',
            'user_id' => '收款人',
            'receiving' => '收款对象',
            'add_time' => '收款时间',
        ];
    }
    
    /**
     * 添加Log
     * @param unknown $params
     * @return boolean
     */
    public function addLog($params) {
        $this->order_id = $params['order_id'];
        $this->order_sn = $params['order_sn'];
        $this->count = $params['count'];
        $this->amount = $params['amount'];
        $this->user_id = Yii::$app->user->id;
        $this->receiving = $params['receiving'];
        $this->add_time = time();
        return $this->save();
    }
    
    /**
     * 用户名
     * @return \yii\db\ActiveQuery
     */
    public function getUserTrueName() {
        return $this->hasOne(AdminUser::className(), ['id' => 'user_id']);
    }
}
