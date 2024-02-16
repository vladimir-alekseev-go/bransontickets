<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_prices`.
 */
class m230609_103836_create_tr_prices_table extends Migration
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
            'tr_prices',
            [
                'id'                    => $this->primaryKey(),
                'id_external'           => $this->integer(11)->notNull(),
                'hash'                  => $this->string(32)->notNull(),
                'hash_summ'             => $this->string(32)->notNull(),
                'start'                 => $this->datetime()->notNull(),
                'end'                   => $this->datetime()->null(),
                'name'                  => $this->string(128)->notNull(),
                'description'           => $this->string(128)->null(),
                'retail_rate'           => $this->decimal(8, 2)->notNull(),
                'special_rate'          => $this->decimal(8, 2)->null(),
                'tripium_rate'          => $this->decimal(8, 2)->null(),
                'available'             => $this->integer(11)->notNull(),
                'sold'                  => $this->integer(11)->notNull(),
                'stop_sell'             => $this->tinyInteger(4)->notNull(),
                'price'                 => $this->decimal(8, 2)->notNull()->defaultValue(0.00),
                'free_sell'             => $this->tinyInteger(4)->notNull()->defaultValue(0),
                'allotment_external_id' => $this->integer(11)->notNull(),
                'price_external_id'     => $this->integer(11)->notNull(),
                'rank_level'            => $this->integer()->notNull()->defaultValue(999999),
            ],
            $tableOptions
        );

        $this->createIndex(
            'id_external',
            'tr_prices',
            'id_external'
        );

        $this->addForeignKey(
            'tr_prices_ibfk_1',
            'tr_prices',
            'id_external',
            'tr_shows',
            'id_external',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'start',
            'tr_prices',
            'start'
        );
        $this->createIndex(
            'idx-tr_prices-price_external_id',
            'tr_prices',
            'price_external_id'
        );
        $this->createIndex(
            'idx-tr_prices-name',
            'tr_prices',
            'name'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_prices');
    }
}
