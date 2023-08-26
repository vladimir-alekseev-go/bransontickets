<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_pos_hotels_photo_join`.
 */
class m230819_140918_create_tr_pos_hotels_photo_join_table extends Migration
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
        $this->createTable('tr_pos_hotels_photo_join', [
            'id'                    => $this->primaryKey(),
            'preview_id'            => $this->integer()->null(),
            'photo_id'              => $this->integer()->null(),
            'item_id'               => $this->integer()->notNull(),
            'activity'              => $this->integer(1)->notNull()->defaultValue(1),
            'sort'                  => $this->integer()->notNull()->defaultValue(0),
            'subcategory'           => $this->string(16)->null(),
            'room_type_external_id' => $this->integer()->null(),
            'hash'                  => $this->string(32)->notNull(),
            'tags'                  => $this->string(128)
        ], $tableOptions);

        $this->createIndex('idx-p_h_p_j-preview_id', 'tr_pos_hotels_photo_join', 'preview_id');
        $this->createIndex('idx-p_h_p_j-photo_id', 'tr_pos_hotels_photo_join', 'photo_id');
        $this->createIndex('idx-p_h_p_j-item_id', 'tr_pos_hotels_photo_join', 'item_id');
        $this->createIndex('idx-p_h_p_j-room_type_external_id', 'tr_pos_hotels_photo_join', 'room_type_external_id');

        $this->addForeignKey(
            'fk-p_h_p_j-preview_id',
            'tr_pos_hotels_photo_join',
            'preview_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk-p_h_p_j-photo_id',
            'tr_pos_hotels_photo_join',
            'photo_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk-p_h_p_j-item_id',
            'tr_pos_hotels_photo_join',
            'item_id',
            'tr_pos_hotels',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-p_h_p_j-room_type_external_id',
            'tr_pos_hotels_photo_join',
            'room_type_external_id',
            'tr_pos_room_types',
            'id_external',
            'SET NULL',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_pos_hotels_photo_join');
    }
}
