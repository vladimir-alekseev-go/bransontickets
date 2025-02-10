<?php

use yii\db\Migration;

/**
 * Class m250109_150619_add_rating_to_pos_hotel
 */
class m250109_150619_add_rating_to_pos_hotel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('tr_pos_hotels', 'rating', $this->integer(1)->notNull()->defaultValue(5));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m250109_150619_add_rating_to_pos_hotel cannot be reverted.\n";

        return false;
    }
}
