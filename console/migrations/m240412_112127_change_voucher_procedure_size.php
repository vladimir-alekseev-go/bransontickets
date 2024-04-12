<?php

use yii\db\Migration;

/**
 * Class m240412_112127_change_voucher_procedure_size
 */
class m240412_112127_change_voucher_procedure_size extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tr_shows', 'voucher_procedure', $this->string(2048)->null());
        $this->alterColumn('tr_attractions', 'voucher_procedure', $this->string(2048)->null());
        $this->alterColumn('tr_attractions', 'hours', $this->string(2048)->null());
        $this->alterColumn('tr_hotels', 'voucher_procedure', $this->string(2048)->null());
        $this->alterColumn('tr_pos_hotels', 'voucher_procedure', $this->string(2048)->null());
        $this->alterColumn('tr_pos_pl_hotels', 'voucher_procedure', $this->string(2048)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240412_112127_change_voucher_procedure_size cannot be reverted.\n";

        return false;
    }
}
