<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $ip
 * @property string $date
 * @property string $code
 * @property string $size
 * @property string $url
 * @property string $page
 * @property string $browser
 * @property string $platform
 * @property string $user_agent
 */
class LogsNginx extends ActiveRecord
{
    public static function tableName(){
        return 'logs_nginx';
    }

    public function attributeLabels(){
        return [
            'id'=>'ID',
            'ip'=>'IP адрес',
            'date'=>'Дата и время',
            'code'=>'Код ответа',
            'size' => 'Размер',
            'url' => 'URL запроса',
            'page' => 'Страница',
            'browser' => 'Браузер',
            'platform' => 'Операционная система',
            'user_agent' => 'User agent'
        ];
    }

    public function rules()
    {
        return [
            [['code', 'size'], 'number'],
            [['user_agent', 'date', 'ip', 'url', 'page', 'browser', 'platform'], 'string'],
        ];
    }

}
