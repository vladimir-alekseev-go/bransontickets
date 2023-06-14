<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_categories`.
 */
class m230609_103322_create_tr_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/tr_categories.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
