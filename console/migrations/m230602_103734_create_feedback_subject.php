<?php

use yii\db\Migration;

/**
 * Class m230602_103734_create_feedback_subject
 */
class m230602_103734_create_feedback_subject extends Migration
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
            'feedback_subject',
            [
                'id'    => $this->primaryKey(11),
                'name'  => $this->string(128)->null(),
                'email' => $this->string(128)->null(),
            ],
            $tableOptions
        );
//        $this->createIndex(
//            'idx-feedback_subject-id',
//            'feedback_subject',
//            'id'
//        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('feedback_subject');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230602_103734_create_feedback_subject cannot be reverted.\n";

        return false;
    }
    */
}
