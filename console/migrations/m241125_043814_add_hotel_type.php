<?php

use yii\db\Migration;

/**
 * Class m241125_043814_add_hotel_type
 */
class m241125_043814_add_hotel_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('tr_pos_hotels', 'price_line', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m241125_043814_add_hotel_type cannot be reverted.\n";

        return false;
    }
}
