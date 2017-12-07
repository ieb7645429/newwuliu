<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bank_info".
 *
 * @property int $bank_info_id
 * @property int $user_id user表关联id
 * @property string $bank_info_card_no 银行卡号
 * @property string $bank_info_account_name 开户名
 * @property string $bank_info_bank_name 开户行
  * @property string $bank_info_bank_address 开户行地址
    * @property string $bank_info_place 落地点
 */
class BankInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'bank_info_card_no', 'bank_info_account_name','bank_info_bank_name'], 'required'],
            [['user_id'], 'integer'],
            [['bank_info_card_no', 'bank_info_bank_address'], 'string', 'max' => 255],
            [['bank_info_account_name','bank_info_bank_name','bank_info_place'], 'string', 'max' => 155],
			   [['user_id','bank_info_place'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bank_info_id' => 'ID',
            'user_id' => '用户ID',
            'bank_info_card_no' => '银行卡号',
            'bank_info_account_name' => '开户名',
            'bank_info_bank_name' => '银行',
			'bank_info_bank_address' => '开户行',
            'bank_info_place' => '落地点',
        ];
    }
    
    /**
     * 取得经销商银行信息
     * @param unknown $userId
     */
    public static function getBankInfoByUser($userId) {
       return self::find() -> where('user_id = :user_id', ['user_id' => $userId])
                           -> one();
    }
    
    /**
     * 取得落地点银行信息
     * @param unknown $userId
     */
    public static function getBankInfoByTerminus($terminusId) {
        return self::find() -> where('bank_info_place = :bank_info_place', ['bank_info_place' => 'luodi_' . $terminusId])
                            -> one();
    }
    
    public function getBankInfo($userId){
        $bankInfo = $this->find()->where(['user_id'=>$userId])->all();
        return $bankInfo;
    }
}
