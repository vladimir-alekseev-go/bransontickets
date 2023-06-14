<?php

use yii\db\Migration;

/**
 * Class m230612_013923_redirects
 */
class m230612_013923_redirects extends Migration
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
            'redirects',
            [
                'id' => $this->primaryKey(),
                'status_code' => $this->string(16)->notNull(),
                'old_url' => $this->string(256)->notNull(),
                'new_url' => $this->string(256)->null(),
                'category' => $this->string(16)->null(),
                'item_id' => $this->integer()->null(),
                'created_at' => $this->datetime()->null(),
            ],
            $tableOptions
        );

        $this->createIndex('idx-redirects-old_url', 'redirects', 'old_url', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('redirects');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230612_013923_redirects cannot be reverted.\n";

        return false;
    }
    */
}
