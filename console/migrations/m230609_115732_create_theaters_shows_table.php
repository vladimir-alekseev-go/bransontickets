<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%theaters_shows}}`.
 */
class m230609_115732_create_theaters_shows_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/theaters_shows.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
