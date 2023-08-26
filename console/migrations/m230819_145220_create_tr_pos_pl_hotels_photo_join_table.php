<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_pos_pl_hotels_photo_join`.
 */
class m230819_145220_create_tr_pos_pl_hotels_photo_join_table extends Migration
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
            'tr_pos_pl_hotels_photo_join',
            [
                'id'         => $this->primaryKey(),
                'preview_id' => $this->integer()->null(),
                'photo_id'   => $this->integer()->null(),
                'item_id'    => $this->integer()->notNull(),
                'activity'   => $this->integer(1)->notNull()->defaultValue(1),
                'sort'       => $this->integer()->notNull()->defaultValue(0)
            ],
            $tableOptions
        );
        $this->createIndex('idx-p_pl_h_p_j-preview_id', 'tr_pos_pl_hotels_photo_join', 'preview_id');
        $this->createIndex('idx-p_pl_h_p_j-photo_id', 'tr_pos_pl_hotels_photo_join', 'photo_id');
        $this->createIndex('idx-p_pl_h_p_j-item_id', 'tr_pos_pl_hotels_photo_join', 'item_id');

        $this->addForeignKey(
            'fk-p_pl_h_p_j-preview_id',
            'tr_pos_pl_hotels_photo_join',
            'preview_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk-p_pl_h_p_j-photo_id',
            'tr_pos_pl_hotels_photo_join',
            'photo_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk-p_pl_h_p_j-item_id',
            'tr_pos_pl_hotels_photo_join',
            'item_id',
            'tr_pos_pl_hotels',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_pos_pl_hotels_photo_join');
    }
}
