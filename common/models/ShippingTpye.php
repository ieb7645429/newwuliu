<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shipping_tpye".
 *
 * @property int $id
 * @property string $name 运费方式
 * @property int $state 1可用,2不可用
 */
class ShippingTpye extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shipping_tpye';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['state'], 'integer'],
            [['name'], 'string', 'max' => 30],
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
            'state' => 'State',
        ];
    }
    
    /**
     * 运费付款方式列表
     * @return array
     */
    public static function getShippingType() {
        return ArrayHelper::map(ShippingTpye::findAll(['state' => 1]), 'id', 'name') ;
    }
    /**
     * 原返付款方式列表
     * @return array
     */
    public static function getReturnShippingType() {
        return ArrayHelper::map(ShippingTpye::findAll(['and','state'=>1,['<>','id'=>3]]), 'id', 'name') ;
    }
    
    /**
     * 运费付款方式名称
     * @param unknown $id
     * @return string
     */
    public static function getShippingTypeNameById($id) {
        return self::findOne($id)->name;
    }
}
