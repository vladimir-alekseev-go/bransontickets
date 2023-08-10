<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_attractions_availability`.
 */
class m230809_145144_create_tr_attractions_availability_table extends Migration
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
            'tr_attractions_availability',
            [
                'id'          => $this->primaryKey(),
                'id_external' => $this->integer(11)->notNull(),
                'date'        => $this->datetime()->notNull(),
            ],
            $tableOptions
        );

        $this->createIndex(
            'id_external',
            'tr_attractions_availability',
            'id_external'
        );

        $this->addForeignKey(
            'tr_attractions_availability_ibfk_1',
            'tr_attractions_availability',
            'id_external',
            'tr_admissions',
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
        $this->dropTable('tr_attractions_availability');
    }
}
