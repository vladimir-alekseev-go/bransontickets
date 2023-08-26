<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_pos_room_types`.
 */
class m230819_140859_create_tr_pos_room_types_table extends Migration
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
        $this->createTable('tr_pos_room_types', [
            'id'               => $this->primaryKey(),
            'id_external'      => $this->integer()->null(),
            'id_external_item' => $this->integer()->null(),
            'name'             => $this->string(64)->notNull(),
            'hash_summ'        => $this->string(32)->notNull(),
            'tags'             => $this->string(128)
        ], $tableOptions);

        $this->createIndex('idx-pos_room_types-id_external', 'tr_pos_room_types', 'id_external');
        $this->createIndex('idx-pos_room_types-id_external_item', 'tr_pos_room_types', 'id_external_item');
        $this->addForeignKey(
            'fk-pos_room_types-id_external_item',
            'tr_pos_room_types',
            'id_external_item',
            'tr_pos_hotels',
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
        $this->dropTable('tr_pos_room_types');
    }
}
