<?php

use yii\db\Migration;

/**
 * Class m241022_125555_any_time_for_show_price
 */
class m241022_125555_any_time_for_show_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tr_prices', 'any_time', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tr_prices', 'any_time');
    }
}
