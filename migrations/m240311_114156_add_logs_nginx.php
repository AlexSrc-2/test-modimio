<?php

use yii\db\Migration;

/**
 * Class m240311_114156_add_logs_nginx
 */
class m240311_114156_add_logs_nginx extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('logs_nginx', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(),
            'date' => $this->dateTime(),
            'code' => $this->integer(),
            'size' => $this->integer(),
            'url' => $this->text(),
            'page' => $this->text(),
            'browser' => $this->string(),
            'platform' => $this->string(),
            'user_agent' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('logs_nginx');
    }
}
