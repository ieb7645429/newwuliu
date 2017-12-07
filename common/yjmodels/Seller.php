<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_seller".
 *
 * @property string $seller_id 卖家编号
 * @property string $seller_name 卖家用户名
 * @property string $member_id 用户编号
 * @property string $seller_group_id 卖家组编号
 * @property string $store_id 店铺编号
 * @property int $is_admin 是否管理员(0-不是 1-是)
 * @property string $seller_quicklink 卖家快捷操作
 * @property string $last_login_time 最后登录时间
 * @property string $seller_level 0不可查看全国，1可产看全国
 */
class Seller extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_seller';
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
            [['seller_name', 'member_id', 'seller_group_id', 'store_id', 'is_admin'], 'required'],
            [['member_id', 'seller_group_id', 'store_id', 'is_admin', 'last_login_time', 'seller_level'], 'integer'],
            [['seller_name'], 'string', 'max' => 50],
            [['seller_quicklink'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'seller_id' => 'Seller ID',
            'seller_name' => 'Seller Name',
            'member_id' => 'Member ID',
            'seller_group_id' => 'Seller Group ID',
            'store_id' => 'Store ID',
            'is_admin' => 'Is Admin',
            'seller_quicklink' => 'Seller Quicklink',
            'last_login_time' => 'Last Login Time',
            'seller_level' => 'Seller Level',
        ];
    }
    
    /**
     * 靳健
     * 物流会员账号修改,同时更改友件seller信息
     * @param unknown $model
     */
    public function sellerInfoEdit($model){
        $seller_model = $this::findOne(['seller_name'=>$model->getOldAttribute('username')]);
        if(!empty($seller_model)){
            $seller_model->seller_name = $model->username;
            return $seller_model->save();
        }
        return true;
    }
}
