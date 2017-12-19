<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticsRoute;

/**
 * RouteSearch represents the model behind the search form of `common\models\LogisticsRoute`.
 */
class RouteSearch extends LogisticsRoute
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logistics_route_id', 'same_city'], 'integer'],
            [['logistics_route_code', 'logistics_route_no', 'logistics_route_name', 'province_id', 'city_id', 'area_id', 'pinyin_name', 'username'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['province_id', 'city_id', 'area_id', 'pinyin_name', 'username']);
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
        $query = LogisticsRoute::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                //the backend need to be consistent with the frontend
                'attributes' => ['logistics_route_id','logistics_route_name'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'logistics_route.logistics_route_id' => $this->logistics_route_id,
            'same_city' => $this->same_city,
//            'driver_id' => $this->driver_id,
            'area.pinyin_name' => $this->pinyin_name,
//            'username' => $this->routeOfCar->carOfDriver->driverJoinUser->username,//,  getRouteOfCar, carOfDriver
            'username' => $this->username,//,  getRouteOfCar, carOfDriver
        ]);


        $query->orFilterWhere([
            'ar.pinyin_name' => $this->pinyin_name,
        ]);

//        $query->andFilterWhere(['like', 'driver.driver_id',$this->getAttribute('driver.driver_id')]);

        //判断根据区还是市



        //innerJoin
        $query->join('LEFT JOIN','logistics_area','logistics_area.logistics_route_id = logistics_route.logistics_route_id')
            ->join('LEFT JOIN','area','logistics_area.area_id = area.area_id')
            ->join('LEFT JOIN','area AS ar','ar.area_id = logistics_area.city_id')
            ->join('LEFT JOIN', 'logistics_car', 'logistics_car.logistics_route_id = logistics_route.logistics_route_id')
            ->join('LEFT JOIN', 'driver', 'driver.logistics_car_id = logistics_car.logistics_car_id')
            ->join('LEFT JOIN', 'user', 'driver.member_id = user.id');


        $query->select(['logistics_route.*', 'area.pinyin_name']);

        $query->andFilterWhere(['like', 'logistics_route_code', $this->logistics_route_code])
            ->andFilterWhere(['like', 'logistics_route_no', $this->logistics_route_no])
            ->andFilterWhere(['like', 'logistics_route_name', $this->logistics_route_name]);

        $dataProvider->sort->defaultOrder = ['logistics_route_id' => SORT_DESC];

        return $dataProvider;
    }
}
