<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_orders`.
 */
class m230917_120332_create_tr_orders_table extends Migration
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
            'tr_orders',
            [
                'id'              => $this->primaryKey(),
                'tripium_user_id' => $this->integer(11)->notNull(),
                'data'            => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->notNull(),
                'hash_summ'       => $this->string(32)->notNull(),
                'order_number'    => $this->string(16)->notNull(),
                'created_at'      => $this->datetime()->notNull(),
                'past'            => $this->tinyInteger(4)->notNull(),
                'discount'        => $this->decimal(7, 2)->null(),
                'coupon'          => $this->decimal(7, 2)->null(),
                'updated_at'      => $this->datetime()->null(),
                'sdc_vouchers'    => $this->text()->null(),
            ],
            $tableOptions
        );

        $this->createIndex('tripium_user_id', 'tr_orders', 'tripium_user_id', false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_orders');
    }
}
