<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_categories`.
 */
class m230609_103322_create_tr_categories_table extends Migration
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
            'tr_categories',
            [
                'id'               => $this->primaryKey(),
                'id_external'      => $this->integer(11)->notNull(),
                'name'             => $this->string(64)->null(),
                'hash_summ'        => $this->string(32)->notNull(),
                'sort_shows'       => $this->integer(4)->defaultValue(500),
                'sort_attractions' => $this->integer(4)->defaultValue(500),
                'sort_hotels'      => $this->integer(4)->defaultValue(500),
            ],
            $tableOptions
        );

        $this->createIndex(
            'id_external',
            'tr_categories',
            'id_external'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_categories');
    }
}
