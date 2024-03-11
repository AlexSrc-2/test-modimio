<?php

namespace app\models\search;

use app\models\LogsNginx;
use kartik\daterange\DateRangeBehavior;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class LogsNginxSearch extends LogsNginx
{
    public $rangeDate;
    public $startDate;
    public $endDate;
    public $dateDay;
    public $count;
    public $urlPopular;
    public $browserPopular;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'rangeDate',
                'dateStartAttribute' => 'startDate',
                'dateEndAttribute' => 'endDate',
            ]
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['dateDay', 'urlPopular', 'browserPopular', 'rangeDate', 'startDate', 'endDate'], 'string'],
            ['count', 'number']
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'dateDay' => 'Дата',
            'count' => 'Число запросов',
            'urlPopular' => 'Самый популярный URL',
            'browserPopular' => 'Самый популярный браузер',
            'rangeDate' => 'Период логов'
        ]);
    }

    public function getDataForChart(array $params = []): array
    {
        $this->load($params);
        $subQuery = self::find()
            ->select('browser')
            ->groupBy('browser')
            ->orderBy(new Expression('count(*) desc'))
            ->limit(3);

        $query = self::find()
            ->select([
                'DATE(date) as dateDay',
                'logs_nginx.browser',
                new Expression('(COUNT(*) / (select COUNT(*) from logs_nginx where date BETWEEN dateDay AND DATE_ADD(`dateDay`, INTERVAL 1 DAY))) * 100 AS precent'),
            ])
            ->innerJoin(['ln2' => $subQuery], 'ln2.browser = logs_nginx.browser');


        $query->andfilterWhere([
            'id' => $this->id,
            'ip' => $this->ip,
            'date' => $this->date,
            'platform' => $this->platform,
            'url' => $this->url,
        ]);

        if ($this->startDate && $this->endDate) {
            $query->andWhere([
                'between',
                'date',
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }

        return $query->groupBy(['dateDay', 'logs_nginx.browser'])
            ->orderBy('dateDay')
            ->asArray()
            ->all();
    }

    public function search(array $params = []): ActiveDataProvider
    {
       $query = self::find()
           ->select([
               'DATE(date) as dateDay',
               'COUNT(*) as count',
               new Expression('
                (
                   SELECT lg.url
                   FROM logs_nginx as lg
                   WHERE lg.date BETWEEN dateDay AND DATE_ADD(`dateDay`, INTERVAL 1 DAY)
                   GROUP BY lg.url
                   ORDER BY COUNT(*)
                   LIMIT 1
               ) as urlPopular
               '),
               new Expression('
                (
                   SELECT lg.browser
                   FROM logs_nginx as lg
                   WHERE lg.date BETWEEN dateDay AND DATE_ADD(`dateDay`, INTERVAL 1 DAY)
                   GROUP BY lg.browser
                   ORDER BY COUNT(*)
                   LIMIT 1
               ) as browserPopular
               ')
           ]);

       $dataProvider = new ActiveDataProvider([
           'query' => $query,
           'pagination' => [
               'pageSize' => 5,
               'pageParam' => 'logs-nginx'
           ],
           'sort' => [
               'attributes' => [
                   'dateDay' => [
                       'asc' => [
                           'dateDay' => SORT_ASC
                       ],
                       'desc' => [
                           'dateDay' => SORT_DESC
                       ]
                   ],
                   'count' => [
                       'asc' => [
                           'count' => SORT_ASC
                       ],
                       'desc' => [
                           'count' => SORT_DESC
                       ]
                   ],
                   'urlPopular' => [
                       'asc' => [
                           'urlPopular' => SORT_ASC
                       ],
                       'desc' => [
                           'urlPopular' => SORT_DESC
                       ]
                   ],
                   'browserPopular' => [
                       'asc' => [
                           'browserPopular' => SORT_ASC
                       ],
                       'desc' => [
                           'browserPopular' => SORT_DESC
                       ]
                   ],
               ]
           ]
       ]);

       $this->load($params);

       $query->andfilterWhere([
           'id' => $this->id,
           'ip' => $this->ip,
           'date' => $this->date,
           'platform' => $this->platform,
           'url' => $this->url,
       ]);

       if ($this->startDate && $this->endDate) {
           $query->andWhere([
               'between',
               'date',
               $this->startDate . ' 00:00:00',
               $this->endDate . ' 23:59:59'
           ]);
       }

       $query->groupBy(['dateDay']);

       return $dataProvider;
    }
}
