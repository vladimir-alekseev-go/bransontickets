<?php

use yii\db\Migration;

/**
 * Class m230612_025737_create_site_sections
 */
class m230612_025737_create_site_sections extends Migration
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
        $this->createTable('site_section', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'url' => $this->string(64)->notNull(),
            'section' => $this->string(16)->notNull(),
            'status' => $this->integer(1)->notNull(),
            'sort' => $this->integer()->notNull(),
            'created_at' => $this->datetime()->null(),
            'updated_at' => $this->datetime()->null(),
        ], $tableOptions);
        $this->createIndex('idx-site_section-section', 'site_section', 'section', true);
        $this->createIndex('idx-site_section-url', 'site_section', 'url', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230612_025737_create_site_sections cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230612_025737_create_site_sections cannot be reverted.\n";

        return false;
    }
    */
}
