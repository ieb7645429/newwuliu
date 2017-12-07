<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string $name 网点名称
 * @property string $customer_type 客户类型
 * @property string $customer_num 客户编号
 * @property string $customer_name 客户名称
 * @property string $contact_person 联系人
 * @property string $telephone 座机
 * @property string $mobilephone 手机
 * @property string $address 地址
 * @property string $coord 坐标
 * @property string $create_time 创建时间
 * @property string $remarks 备注
 * @property string $route 所属线路
 * @property string $maccount_having 是否有商城账号
 * @property string $mall_account 商城账号
 * @property string $open_up 是否开通
 * @property string $collection 是否采集
 * @property string $ultimate 是否旗舰
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
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
            [['name', 'customer_name', 'route'], 'string', 'max' => 32],
            [['customer_type', 'open_up', 'collection', 'ultimate'], 'string', 'max' => 4],
            [['customer_num'], 'string', 'max' => 19],
            [['contact_person'], 'string', 'max' => 16],
            [['telephone'], 'string', 'max' => 35],
            [['mobilephone'], 'string', 'max' => 11],
            [['address'], 'string', 'max' => 33],
            [['coord', 'remarks'], 'string', 'max' => 64],
            [['create_time'], 'string', 'max' => 15],
            [['maccount_having'], 'string', 'max' => 8],
            [['mall_account'], 'string', 'max' => 18],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'customer_type' => 'Customer Type',
            'customer_num' => 'Customer Num',
            'customer_name' => 'Customer Name',
            'contact_person' => 'Contact Person',
            'telephone' => 'Telephone',
            'mobilephone' => 'Mobilephone',
            'address' => 'Address',
            'coord' => 'Coord',
            'create_time' => 'Create Time',
            'remarks' => 'Remarks',
            'route' => 'Route',
            'maccount_having' => 'Maccount Having',
            'mall_account' => 'Mall Account',
            'open_up' => 'Open Up',
            'collection' => 'Collection',
            'ultimate' => 'Ultimate',
        ];
    }
}
