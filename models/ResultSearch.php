<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Result;

/**
 * ResultSearch represents the model behind the search form about `app\models\Result`.
 */
class ResultSearch extends Result
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'LID', 'Time'], 'integer'],
            [['Number', 'Price', 'Profit'], 'number'],
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
        $query = Result::find();

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
            'ID' => $this->ID,
            'LID' => $this->LID,
            'Time' => $this->Time,
            'Number' => $this->Number,
            'Price' => $this->Price,
            'Profit' => $this->Profit,
        ]);

        return $dataProvider;
    }
}
