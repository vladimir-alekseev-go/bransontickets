<?php

use yii\db\Migration;

/**
 * Class m230612_033150_create_tr_shows_similar
 */
class m230612_033150_create_tr_shows_similar extends Migration
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
        $this->createTable('tr_shows_similar', [
            'id' => $this->primaryKey(),
            'external_id' => $this->integer()->notNull(),
            'similar_external_id' => $this->integer()->notNull(),
            'created_at' => $this->datetime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-ss-external_id', 'tr_shows_similar', 'external_id');
        $this->createIndex('idx-ss-similar_external_id', 'tr_shows_similar', 'similar_external_id');

        $this->addForeignKey(
            'fk-ss-external_id',
            'tr_shows_similar',
            'external_id',
            'tr_shows',
            'id_external',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-ss-similar_external_id',
            'tr_shows_similar',
            'similar_external_id',
            'tr_shows',
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
        echo "m230612_033150_create_tr_shows_similar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230612_033150_create_tr_shows_similar cannot be reverted.\n";

        return false;
    }
    */
}
