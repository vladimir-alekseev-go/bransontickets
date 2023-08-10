<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_attractions_categories`.
 */
class m230809_145109_create_tr_attractions_categories_table extends Migration
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
            'tr_attractions_categories',
            [
                'id'                   => $this->primaryKey(),
                'id_external_show'     => $this->integer(11)->notNull(),
                'id_external_category' => $this->integer(11)->notNull(),
            ],
            $tableOptions
        );

        $this->createIndex(
            'id_external_show',
            'tr_attractions_categories',
            'id_external_show'
        );

        $this->addForeignKey(
            'tr_attractions_categories_ibfk_1',
            'tr_attractions_categories',
            'id_external_show',
            'tr_attractions',
            'id_external',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'id_external_category',
            'tr_attractions_categories',
            'id_external_category'
        );

        $this->addForeignKey(
            'tr_attractions_categories_ibfk_2',
            'tr_attractions_categories',
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
        $this->dropTable('tr_attractions_categories');
    }
}
