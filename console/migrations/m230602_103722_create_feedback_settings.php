<?php

use yii\db\Migration;

/**
 * Class m230602_103722_create_feedback_settings
 */
class m230602_103722_create_feedback_settings extends Migration
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
            'feedback_settings',
            [
                'id'      => $this->primaryKey(),
                'address' => $this->string(256)->null(),
                'phone'   => $this->string(64)->null(),
                'email'   => $this->string(128)->null(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230602_103722_create_feedback_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230602_103722_create_feedback_settings cannot be reverted.\n";

        return false;
    }
    */
}
