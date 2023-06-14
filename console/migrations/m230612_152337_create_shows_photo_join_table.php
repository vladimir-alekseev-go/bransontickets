<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shows_photo_join`.
 */
class m230612_152337_create_shows_photo_join_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ . '/sql/shows_photo_join.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
