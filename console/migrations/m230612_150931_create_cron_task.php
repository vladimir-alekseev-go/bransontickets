<?php

use yii\db\Migration;

/**
 * Class m230612_150931_create_cron_task
 */
class m230612_150931_create_cron_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(
            'cron_task',
            [
                'id' => $this->primaryKey(),
                'type' => $this->string(16),
                'data' => $this->text()->null(),
                'status' => $this->string(16),
                'created_at' => $this->datetime()->null(),
                'started_at' => $this->datetime()->null(),
                'finished_at' => $this->datetime()->null(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230612_150931_create_cron_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230612_150931_create_cron_task cannot be reverted.\n";

        return false;
    }
    */
}
