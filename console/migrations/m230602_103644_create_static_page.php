<?php

use yii\db\Migration;

/**
 * Class m230602_103644_create_static_page
 */
class m230602_103644_create_static_page extends Migration
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
            'static_page',
            [
                'id'         => $this->primaryKey(),
                'url'        => $this->string(64)->notNull(),
                'title'      => $this->string(64)->notNull(),
                'status'     => $this->integer(1)->notNull(),
                'text'       => $this->text()->notNull(),
                'created_at' => $this->datetime()->null(),
                'updated_at' => $this->datetime()->null(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('static_page');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230602_103644_create_static_page cannot be reverted.\n";

        return false;
    }
    */
}
