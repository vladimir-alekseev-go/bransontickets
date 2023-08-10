<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_attractions_prices`.
 */
class m230809_145130_create_tr_attractions_prices_table extends Migration
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
            'tr_attractions_prices',
            [
                'id'                => $this->primaryKey(),
                'id_external'       => $this->integer(11)->notNull(),
                'hash'              => $this->string(32)->notNull(),
                'hash_summ'         => $this->string(32)->notNull(),
                'start'             => $this->datetime()->notNull(),
                'end'               => $this->datetime()->null(),
                'name'              => $this->string(128)->notNull(),
                'description'       => $this->string(128)->null(),
                'retail_rate'       => $this->decimal(8, 2)->notNull(),
                'special_rate'      => $this->decimal(8, 2)->null(),
                'tripium_rate'      => $this->decimal(8, 2)->null(),
                'available'         => $this->integer(11)->notNull(),
                'sold'              => $this->integer(11)->notNull(),
                'stop_sell'         => $this->tinyInteger(4)->notNull(),
                'free_sell'         => $this->tinyInteger(4)->notNull()->defaultValue(0),
                'price'             => $this->decimal(8, 2)->notNull()->defaultValue(0.00),
                'any_time'          => $this->tinyInteger(1)->notNull()->defaultValue(0),
                'price_external_id' => $this->integer(11)->notNull(),
                'rank'              => $this->integer(11)->notNull(),
                'alternative_rate'  => $this->decimal(8, 2)->null(),
            ],
            $tableOptions
        );

        $this->createIndex(
            'id_external',
            'tr_attractions_prices',
            'id_external'
        );

        $this->addForeignKey(
            'tr_attractions_prices_ibfk_1',
            'tr_attractions_prices',
            'id_external',
            'tr_admissions',
            'id_external',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-tr_a_prices-price_external_id',
            'tr_attractions_prices',
            'price_external_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_attractions_prices');
    }
}
