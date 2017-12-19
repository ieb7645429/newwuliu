<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistics_area".
 *
 * @property int $id
 * @property int $logistics_route_id 物流线路id
 * @property int $province_id 省id
 * @property int $city_id 市id
 * @property int $area_id 区id
 */
class LogisticsArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logistics_route_id'], 'required'],
            [['logistics_route_id', 'province_id', 'city_id', 'area_id'], 'integer'],
            [['member_provinceid', 'member_cityid', 'member_areaid'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['member_provinceid', 'member_cityid', 'member_areaid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logistics_route_id' => 'Logistics Route ID',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'area_id' => 'Area ID',
        ];
    }

    /**
     * 获取省市区
     * @Author:Fenghuan
     * @param $fields
     * @param $condition
     * @return array|null|\yii\db\ActiveRecord
     * @internal param $id
     */
    public function findOneModel($fields, $condition)
    {
        return self::find()->select($fields)->where($condition)->one();
    }

    /**
     * Update
     * @Author:Fenghuan
     * @param $data
     * @param $condition
     * @return int
     */
    public static function updateLogisticsArea($data, $condition)
    {
        return self::updateAll($data, $condition);
    }

}

























