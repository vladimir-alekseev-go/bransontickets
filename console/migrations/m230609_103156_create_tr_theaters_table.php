<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_theaters`.
 */
class m230609_103156_create_tr_theaters_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/tr_theaters.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
