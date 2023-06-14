<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_prices`.
 */
class m230609_103836_create_tr_prices_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/tr_prices.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
