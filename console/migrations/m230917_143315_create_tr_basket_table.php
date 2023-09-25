<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_basket`.
 */
class m230917_143315_create_tr_basket_table extends Migration
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
            'tr_basket',
            [
                'id'           => $this->primaryKey(),
                'session_id'   => $this->string(16)->notNull(),
                'user_id'      => $this->integer(11)->null(),
                'data'         => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null(),
                'updated_at'   => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'reserve_at'   => $this->datetime()->null(),
                'accept_terms' => $this->integer(1)->null()->defaultValue(0),
                'coupon_data'  => $this->string(2048)->null(),
            ],
            $tableOptions
        );

        $this->createIndex(
            'idx-tr_basket-session_id',
            'tr_basket',
            'session_id'
        );

        $this->createIndex(
            'idx-tr_basket-user_id',
            'tr_basket',
            'user_id'
        );

        $this->addForeignKey(
            'fk-tr_basket-user_id',
            'tr_basket',
            'user_id',
            'users',
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
        $this->dropTable('tr_basket');
    }
}
