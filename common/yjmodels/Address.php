<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_address".
 *
 * @property string $address_id 地址ID
 * @property string $member_id 会员ID
 * @property string $true_name 会员姓名
 * @property string $area_id 地区ID
 * @property int $city_id 市级ID
 * @property string $area_info 地区内容
 * @property string $address 地址
 * @property string $tel_phone 座机电话
 * @property string $mob_phone 手机电话
 * @property string $is_default 1默认收货地址
 * @property int $dlyp_id 自提点ID
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_address';
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
            [['member_id', 'area_id', 'city_id', 'dlyp_id'], 'integer'],
            [['true_name', 'address'], 'required'],
            [['is_default'], 'string'],
            [['true_name'], 'string', 'max' => 50],
            [['area_info', 'address'], 'string', 'max' => 255],
            [['tel_phone'], 'string', 'max' => 20],
            [['mob_phone'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'address_id' => 'Address ID',
            'member_id' => 'Member ID',
            'true_name' => 'True Name',
            'area_id' => 'Area ID',
            'city_id' => 'City ID',
            'area_info' => 'Area Info',
            'address' => 'Address',
            'tel_phone' => 'Tel Phone',
            'mob_phone' => 'Mob Phone',
            'is_default' => 'Is Default',
            'dlyp_id' => 'Dlyp ID',
        ];
    }
}
