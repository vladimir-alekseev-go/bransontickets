<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_attractions_similar`.
 */
class m230809_145155_create_tr_attractions_similar_table extends Migration
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
        $this->createTable('tr_attractions_similar', [
            'id' => $this->primaryKey(),
            'external_id' => $this->integer()->notNull(),
            'similar_external_id' => $this->integer()->notNull(),
            'created_at' => $this->datetime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-as-external_id', 'tr_attractions_similar', 'external_id');
        $this->createIndex('idx-as-similar_external_id', 'tr_attractions_similar', 'similar_external_id');

        $this->addForeignKey(
            'fk-as-external_id',
            'tr_attractions_similar',
            'external_id',
            'tr_attractions',
            'id_external',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-as-similar_external_id',
            'tr_attractions_similar',
            'similar_external_id',
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
        $this->dropTable('tr_attractions_similar');
    }
}
