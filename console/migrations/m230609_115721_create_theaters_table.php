<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%theaters}}`.
 */
class m230609_115721_create_theaters_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/theaters.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
