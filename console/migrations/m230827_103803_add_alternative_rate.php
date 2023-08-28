<?php

use yii\db\Migration;

/**
 * Class m230827_103803_add_alternative_rate
 */
class m230827_103803_add_alternative_rate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tr_prices', 'alternative_rate', $this->decimal(8,2)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190701_144521_add_alternative_rate cannot be reverted.\n";

        return false;
    }
}
