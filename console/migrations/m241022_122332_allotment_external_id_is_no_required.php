<?php

use yii\db\Migration;

/**
 * Class m241022_122332_allotment_external_id_is_no_required
 */
class m241022_122332_allotment_external_id_is_no_required extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tr_prices', 'allotment_external_id', $this->string(36)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('tr_prices', 'allotment_external_id', $this->integer()->notNull());
    }
}
