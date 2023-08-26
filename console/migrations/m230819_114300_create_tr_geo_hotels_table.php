<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_geo_hotels`.
 */
class m230819_114300_create_tr_geo_hotels_table extends Migration
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
            'tr_geo_hotels',
            [
                'id'             => $this->primaryKey(),
                'destination_id' => $this->string(64)->notNull(),
                'description'    => $this->string(128)->notNull(),
                'active'         => $this->tinyInteger(1)->notNull(),
                'hash_summ'      => $this->string(64)->notNull(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_geo_hotels');
    }
}
