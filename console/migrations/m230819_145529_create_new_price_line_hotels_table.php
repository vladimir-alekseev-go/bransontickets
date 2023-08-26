<?php

use yii\db\Migration;

/**
 * Handles the creation of table `new_price_line_hotels`.
 */
class m230819_145529_create_new_price_line_hotels_table extends Migration
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
        $this->createTable('new_price_line_hotels', [
            'id'          => $this->primaryKey(),
            'external_id' => $this->integer()->notNull(),
            'status'      => $this->integer()->notNull(),
            'query'       => $this->string(1028)->notNull(),
            'created_at'  => $this->datetime()->null(),
            'updated_at'  => $this->datetime()->null(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('new_price_line_hotels');
    }
}
