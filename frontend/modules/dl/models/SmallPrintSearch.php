<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\dl\models\SmallPrint;

/**
 * SmallPrintSearch represents the model behind the search form of `common\models\SmallPrint`.
 */
class SmallPrintSearch extends SmallPrint
{
    
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
            [['id', 'print_member_id'], 'integer'],
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
        $query = SmallPrint::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params['params']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        if(!empty($params['print_time'])){
            $query->andFilterWhere(['between', 'print_time', $params['print_time']['start'] , $params['print_time']['end']]);
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'print_time' => $this->print_time,
            'print_member_id' => Yii::$app->user->id,
        ]);
        return $dataProvider;
    }
    
}
