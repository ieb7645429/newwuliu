<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticsOrderEdit;

/**
 * LogisticsOrderEditSearch represents the model behind the search form of `common\models\LogisticsOrderEdit`.
 */
class LogisticsOrderEditSearch extends LogisticsOrderEdit
{
    public function attributes(){
        return array_merge(parent::attributes(),['receivingCityName','memberCityName','receivingProvinceName','userName','trueName','advance','routeName','driverTrueName','operatorName','shippingType']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'goods_num', 'order_state', 'state', 'freight_state', 'goods_price_state', 'abnormal', 'collection', 'order_type', 'member_id', 'member_cityid', 'receiving_provinceid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'logistics_route_id', 'shipping_type', 'employee_id', 'driver_member_id', 'test', 'scale', 'same_city', 'edit_member_id'], 'integer'],
            [['logistics_sn', 'goods_sn', 'order_sn', 'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'receiving_name_area', 'return_logistics_sn','order_remark','receivingCityName','memberCityName','receivingProvinceName','userName','trueName','advance','routeName','driverTrueName','operatorName','shippingType'], 'safe'],
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_one', 'collection_poundage_two', 'shipping_sale'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = LogisticsOrderEdit::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        //0.0发货人市
        $query->join('INNER JOIN','area AS B','B.area_id = logistics_order_edit.member_cityid');
        $query->andFilterWhere(['like','B.area_name',$this->memberCityName]);
        $dataProvider->sort->attributes['memberCityName']=[
            'asc' =>['B.area_name'=>SORT_ASC],
            'desc' =>['B.area_name'=>SORT_DESC],
        ];


        //0.0收货人市
        $query->join('INNER JOIN','area AS A','A.area_id = logistics_order_edit.receiving_cityid');
        $query->andFilterWhere(['like','A.area_name',$this->receivingCityName]);
        $dataProvider->sort->attributes['receivingCityName']=[
            'asc' =>['A.area_name'=>SORT_ASC],
            'desc' =>['A.area_name'=>SORT_DESC],
        ];

        //0.0收货人省
        $query->join('INNER JOIN','area AS C','C.area_id = logistics_order_edit.receiving_provinceid');
        $query->andFilterWhere(['like','C.area_name',$this->receivingProvinceName]);
        $dataProvider->sort->attributes['receivingProvinceName']=[
            'asc' =>['C.area_name'=>SORT_ASC],
            'desc' =>['C.area_name'=>SORT_DESC],
        ];


        //0.0开单员
        $query->join('LEFT JOIN','user as truename','truename.id = logistics_order_edit.employee_id');
        $query->andFilterWhere(['like','truename.user_truename',$this->trueName]);
        $dataProvider->sort->attributes['trueName']=[
            'asc' =>['truename.user_truename'=>SORT_ASC],
            'desc' =>['truename.user_truename'=>SORT_DESC],
        ];

        //0.0司机
        $query->join('LEFT JOIN','user as drivername','drivername.id = logistics_order_edit.driver_member_id');
        $query->andFilterWhere(['like','drivername.user_truename',$this->driverTrueName]);
        $dataProvider->sort->attributes['driverTrueName']=[
            'asc' =>['drivername.username'=>SORT_ASC],
            'desc' =>['drivername.username'=>SORT_DESC],
        ];

        //0.0修改操作员
        $query->join('LEFT JOIN','user as operator','operator.id = logistics_order_edit.edit_member_id');
        $query->andFilterWhere(['like','operator.user_truename',$this->operatorName]);
        $dataProvider->sort->attributes['operatorName']=[
            'asc' =>['operator.username'=>SORT_ASC],
            'desc' =>['operator.username'=>SORT_DESC],
        ];

        //开单时间
        $add_time = ArrayHelper::getValue($params, 'LogisticsOrderEditSearch.add_time', null);
        if($add_time){
            list($start, $end) = explode(' - ', $add_time);
            $start = strtotime($start);
            $end = strtotime($end)+60*60*24;

            $query->andFilterWhere(['between', 'logistics_order_edit.add_time', $start , $end]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'freight' => $this->freight,
            'goods_price' => $this->goods_price,
            'make_from_price' => $this->make_from_price,
            'goods_num' => $this->goods_num,
            'order_state' => $this->order_state,
            'state' => $this->state,
            'freight_state' => $this->freight_state,
            'goods_price_state' => $this->goods_price_state,
            'abnormal' => $this->abnormal,
            'collection' => $this->collection,
            'collection_poundage_one' => $this->collection_poundage_one,
            'collection_poundage_two' => $this->collection_poundage_two,
            'order_type' => $this->order_type,
            'add_time' => $this->add_time,
            'member_id' => $this->member_id,
            'member_cityid' => $this->member_cityid,
            'receiving_provinceid' => $this->receiving_provinceid,
            'receiving_cityid' => $this->receiving_cityid,
            'receiving_areaid' => $this->receiving_areaid,
            'terminus_id' => $this->terminus_id,
            'logistics_route_id' => $this->logistics_route_id,
            'shipping_type' => $this->shipping_type,
            'employee_id' => $this->employee_id,
            'driver_member_id' => $this->driver_member_id,
            'test' => $this->test,
            'shipping_sale' => $this->shipping_sale,
            'scale' => $this->scale,
            'same_city' => $this->same_city,
            'edit_member_id' => $this->edit_member_id,
        ]);

        $query->andFilterWhere(['like', 'logistics_sn', $this->logistics_sn])
            ->andFilterWhere(['like', 'goods_sn', $this->goods_sn])
            ->andFilterWhere(['like', 'order_sn', $this->order_sn])
            ->andFilterWhere(['like', 'member_name', $this->member_name])
            ->andFilterWhere(['like', 'member_phone', $this->member_phone])
            ->andFilterWhere(['like', 'receiving_name', $this->receiving_name])
            ->andFilterWhere(['like', 'receiving_phone', $this->receiving_phone])
            ->andFilterWhere(['like', 'receiving_name_area', $this->receiving_name_area])
            ->andFilterWhere(['like', 'return_logistics_sn', $this->return_logistics_sn])
            ->andFilterWhere(['like', 'edit_time', $this->edit_time])
            ->andFilterWhere(['like', 'order_remark', $this->order_remark]);

        return $dataProvider;
    }
}
