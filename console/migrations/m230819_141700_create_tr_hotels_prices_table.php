<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_hotels_prices`.
 */
class m230819_141700_create_tr_hotels_prices_table extends Migration
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
        $fields = [
            'id'                => $this->primaryKey(),
            'id_external'       => $this->integer()->null(),
            'name'              => $this->string(128)->notNull(),
            'hash'              => $this->string(32)->notNull(),
            'hash_summ'         => $this->string(32)->notNull(),
            'start'             => $this->datetime()->notNull(),
            'end'               => $this->datetime()->null(),
            'description'       => $this->string(128)->null(),
            'retail_rate'       => $this->decimal(8, 2)->notNull(),
            'special_rate'      => $this->decimal(8, 2)->null(),
            'tripium_rate'      => $this->decimal(8, 2)->null(),
            'available'         => $this->integer()->null(),
            'sold'              => $this->integer()->null(),
            'stop_sell'         => $this->integer(1)->notNull()->defaultValue(0),
            'free_sell'         => $this->integer(1)->notNull()->defaultValue(0),
            'any_time'          => $this->integer(1)->notNull()->defaultValue(0),
            'price'             => $this->decimal(8, 2)->notNull(),
            'price_external_id' => $this->integer()->notNull(),
            'rank'              => $this->integer()->notNull()->defaultValue(999999),
            'alternative_rate'  => $this->decimal(8, 2)->null(),
            'capacity'          => $this->integer()->null(),
        ];
        $this->createTable('tr_pos_hotels_price_room', $fields, $tableOptions);
        $this->createTable('tr_pos_hotels_price_extra', $fields, $tableOptions);
        $this->createIndex('idx-pos_hotels_price_r-id_external', 'tr_pos_hotels_price_room', 'id_external');
        $this->createIndex('idx-pos_hotels_price_e-id_external', 'tr_pos_hotels_price_extra', 'id_external');
        $this->createIndex('idx-pos_hotels_price_r-hash', 'tr_pos_hotels_price_room', 'hash', true);
        $this->createIndex('idx-pos_hotels_price_e-hash', 'tr_pos_hotels_price_extra', 'hash', true);
        $this->createIndex('idx-pos_h_p_r-price_external_id', 'tr_pos_hotels_price_room', 'price_external_id');
        $this->createIndex('idx-pos_h_p_e-price_external_id', 'tr_pos_hotels_price_extra', 'price_external_id');
        $this->addForeignKey(
            'fk-pos_hotels_price_r-id_external',
            'tr_pos_hotels_price_room',
            'id_external',
            'tr_pos_room_types',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-pos_hotels_price_e-id_external',
            'tr_pos_hotels_price_extra',
            'id_external',
            'tr_pos_room_types',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_pos_hotels_price_room');
        $this->dropTable('tr_pos_hotels_price_extra');
    }
}
