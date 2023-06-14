<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_shows_categories`.
 */
class m230612_030431_create_tr_shows_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/tr_shows_categories.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
