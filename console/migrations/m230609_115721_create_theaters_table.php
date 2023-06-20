<?php

use yii\db\Migration;

/**
 * Handles the creation of table `theaters`.
 */
class m230609_115721_create_theaters_table extends Migration
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
            'theaters',
            [
                'id'     => $this->primaryKey(),
                'name'   => $this->string(64)->notNull(),
                'domain' => $this->string(2048)->notNull(),
                'site'   => $this->string(64)->notNull(),
                'phone'  => $this->string(64)->notNull(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('theaters');
    }
}
