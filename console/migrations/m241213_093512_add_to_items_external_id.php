<?php

use yii\db\Migration;

/**
 * Class m241213_093512_add_to_items_external_id
 */
class m241213_093512_add_to_items_external_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('tr_shows', 'external_id', $this->string(10)->notNull()->after('id_external'));
        $this->addColumn('tr_attractions', 'external_id', $this->string(10)->notNull()->after('id_external'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('tr_shows', 'external_id');
        $this->dropColumn('tr_attractions', 'external_id');
    }
}
