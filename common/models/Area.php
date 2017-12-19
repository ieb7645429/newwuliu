<?php

namespace common\models;
use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property string $area_id 索引ID
 * @property string $area_name 地区名称
 * @property string $area_parent_id 地区父ID
 * @property int $area_sort 排序
 * @property int $area_deep 地区深度，从1开始
 * @property string $area_region 大区名称
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area_name'], 'required'],
            [['area_parent_id', 'area_sort', 'area_deep'], 'integer'],
            [['area_name', 'pinyin_name'], 'string', 'max' => 50],
            [['area_region'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'area_id' => 'Area ID',
            'area_name' => 'Area Name',
            'area_parent_id' => 'Area Parent ID',
            'area_sort' => 'Area Sort',
            'area_deep' => 'Area Deep',
            'area_region' => 'Area Region',
        ];
    }

    /**
     * Return one model
     * @Author:Fenghuan
     * @param $id
     * @return static
     */
    public static function findModel($id)
    {
        return self::findOne($id);
    }

    /**
     * 更新
     * @Author:Fenghuan
     * @param $data
     * @param $condition
     */
    public function updateArea($data, $condition)
    {
        self::updateAll($data, $condition);
    }

    /*
     * 获取地址
     * */
    public static function getRegion($area_parentId=0)
    {
        $result = static::find()->where(['area_parent_id'=>$area_parentId])->asArray()->all();
        return ArrayHelper::map($result, 'area_id', 'area_name');
    }

    /**
     * 获取地址信息
     */
    public function getAreaInfo($condition = array()){
        return $this->find()->where($condition)->one();
    }

    /**
     * @desc 根据Id取得地区名 
     * @param unknown $id
     * @return string
     */
    public static function getAreaNameById($id) {
        $area = static::findOne($id);
        if($area) {
            return $area->area_name;
        }
        return '';
    }
    
    public function getAreaList($key) {
        $query = self::find()->where( ['like', 'pinyin_name', $key.'%', false])
                             ->select('area_id, area_name, area_parent_id, area_deep');
        return $query->asArray()->all();
    }
}
