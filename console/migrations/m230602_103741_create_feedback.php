<?php

use yii\db\Migration;

/**
 * Class m230602_103741_create_feedback
 */
class m230602_103741_create_feedback extends Migration
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
            'feedback',
            [
                'id'         => $this->primaryKey(),
                'name'       => $this->string(128)->notNull(),
                'email'      => $this->string(128)->notNull(),
                'subject_id' => $this->integer(11)->null(),
                'message'    => $this->text()->notNull(),
                'created_at' => $this->datetime()->null(),
            ],
            $tableOptions
        );
        $this->createIndex(
            'idx-feedback-subject_id',
            'feedback',
            'subject_id'
        );

        $this->addForeignKey(
            'fk-feedback-subject_id',
            'feedback',
            'subject_id',
            'feedback_subject',
            'id',
            'SET NULL',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('feedback');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230602_103741_create_feedback cannot be reverted.\n";

        return false;
    }
    */
}
