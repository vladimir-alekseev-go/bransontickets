<?php

use yii\db\Migration;

/**
 * Handles the creation of table `content_files`.
 */
class m230609_102529_create_content_files_table extends Migration
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
            'content_files',
            [
                'id'               => $this->primaryKey(),
                'path'             => $this->string(256)->notNull(),
                'file_name'        => $this->string(128)->notNull(),
                'file_source_name' => $this->string(128)->notNull(),
                'dir'              => $this->string(32)->notNull(),
                'source_url'       => $this->string(256)->null(),
                'source_file_time' => $this->integer(11)->notNull()->defaultValue(0),
                'old'              => $this->integer(11)->null(),
                'path_old'         => $this->string(256)->null(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('content_files');
    }
}
