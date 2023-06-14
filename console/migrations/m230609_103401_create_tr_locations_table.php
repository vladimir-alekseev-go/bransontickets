<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_locations`.
 */
class m230609_103401_create_tr_locations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/tr_locations.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
