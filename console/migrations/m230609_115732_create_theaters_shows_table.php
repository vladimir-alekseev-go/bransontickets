<?php

use yii\db\Migration;

/**
 * Handles the creation of table `theaters_shows`.
 */
class m230609_115732_create_theaters_shows_table extends Migration
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
            'theaters_shows',
            [
                'id'          => $this->primaryKey(),
                'theater_id'  => $this->integer(11)->notNull(),
                'id_external' => $this->integer(11)->notNull(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('theaters_shows');
    }
}
