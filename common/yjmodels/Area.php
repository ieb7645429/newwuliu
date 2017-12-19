<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_area".
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
        return 'qp_area';
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
            [['area_name'], 'required'],
            [['area_parent_id', 'area_sort', 'area_deep'], 'integer'],
            [['area_name'], 'string', 'max' => 50],
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
}
