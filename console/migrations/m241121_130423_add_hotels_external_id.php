<?php

use yii\db\Migration;

/**
 * Class m241121_130423_add_hotels_external_id
 */
class m241121_130423_add_hotels_external_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(): bool
    {
        $this->addColumn('tr_pos_hotels', 'external_id', $this->string(10)->notNull()->after('id_external'));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function down(): bool
    {
        echo "m241121_130423_add_hotels_external_id cannot be reverted.\n";

        return false;
    }
}
