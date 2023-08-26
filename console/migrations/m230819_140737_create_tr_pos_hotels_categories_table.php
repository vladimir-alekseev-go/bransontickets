<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_pos_hotels_categories`.
 */
class m230819_140737_create_tr_pos_hotels_categories_table extends Migration
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
        $this->createTable('tr_pos_hotels_categories', [
            'id'                   => $this->primaryKey(),
            'id_external_show'     => $this->integer()->notNull(),
            'id_external_category' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-tr_pos_hotels-id_external_show', 'tr_pos_hotels_categories', 'id_external_show');
        $this->createIndex('idx-tr_pos_hotels-id_external_category', 'tr_pos_hotels_categories', 'id_external_category');
        $this->addForeignKey(
            'fk-tph_categories-id_external_show',
            'tr_pos_hotels_categories',
            'id_external_show',
            'tr_pos_hotels',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-tph_categories-id_external_category',
            'tr_pos_hotels_categories',
            'id_external_category',
            'tr_categories',
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
        $this->dropTable('tr_pos_hotels_categories');
    }
}
