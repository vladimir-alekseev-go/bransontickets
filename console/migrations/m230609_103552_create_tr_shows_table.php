<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_shows`.
 */
class m230609_103552_create_tr_shows_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/tr_shows.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
