<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bangpai;

/**
 * BangpaiSearch represents the model behind the search form about `app\models\Bangpai`.
 */
class BangpaiSearch extends Bangpai
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'Pay_Time', 'Game_ID', 'Active_Time', 'Balance'], 'integer'],
            [['Name', 'Daqv', 'Fuwuqi', 'Function'], 'safe'],
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
    public function search($params,$sql = [])
    {
        if(empty($sql))$query = Bangpai::find();
        else $query = Bangpai::find()->where($sql);

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
            'Pay_Time' => $this->Pay_Time,
            'Game_ID' => $this->Game_ID,
            'Active_Time' => $this->Active_Time,
            'Balance' => $this->Balance,
        ]);
        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Daqv', $this->Daqv])
            ->andFilterWhere(['like', 'Fuwuqi', $this->Fuwuqi])
            ->andFilterWhere(['like', 'Function', $this->Function]);

        return $dataProvider;
    }
}
