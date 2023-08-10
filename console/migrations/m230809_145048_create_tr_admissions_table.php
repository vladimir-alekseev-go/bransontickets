<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_admissions`.
 */
class m230809_145048_create_tr_admissions_table extends Migration
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
            'tr_admissions',
            [
                'id'               => $this->primaryKey(),
                'id_external'      => $this->integer(11)->notNull(),
                'id_external_item' => $this->integer(11)->notNull(),
                'name'             => $this->string(64)->notNull(),
                'hash_summ'        => $this->string(32)->notNull(),
                'inclusions'       => $this->text()->null(),
                'exclusions'       => $this->text()->null(),
            ],
            $tableOptions
        );

        $this->createIndex(
            'id_external',
            'tr_admissions',
            'id_external'
        );

        $this->createIndex(
            'id_external_item',
            'tr_admissions',
            'id_external_item'
        );

        $this->addForeignKey(
            'tr_admissions_ibfk_1',
            'tr_admissions',
            'id_external_item',
            'tr_attractions',
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
        $this->dropTable('tr_admissions');
    }
}
