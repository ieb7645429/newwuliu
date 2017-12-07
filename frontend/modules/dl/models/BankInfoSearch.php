<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\dl\models\BankInfo;

/**
 * Bankinfosearch represents the model behind the search form of `common\models\Bankinfo`.
 */
class BankInfoSearch extends BankInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bank_info_id', 'user_id'], 'integer'],
            [['bank_info_card_no', 'bank_info_account_name', 'bank_info_bank_name', 'bank_info_bank_address','bank_info_place'], 'safe'],
        ];
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
        $query = Bankinfo::find();

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
        // grid filtering conditions
        $query->andFilterWhere([
            'bank_info_id' => $this->bank_info_id,
            'user_id' => $this->user_id,
			'bank_info_place' => $this->bank_info_place,
        ]);

		$query->andFilterWhere(['like', 'bank_info_card_no', $this->bank_info_card_no])
            ->andFilterWhere(['like', 'bank_info_account_name', $this->bank_info_account_name])
			->andFilterWhere(['like', 'bank_info_bank_name', $this->bank_info_bank_name])
            ->andFilterWhere(['like', 'bank_info_bank_address', $this->bank_info_bank_address]);

        return $dataProvider;
    }
}
