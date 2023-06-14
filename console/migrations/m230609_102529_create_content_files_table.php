<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content_files}}`.
 */
class m230609_102529_create_content_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/content_files.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
