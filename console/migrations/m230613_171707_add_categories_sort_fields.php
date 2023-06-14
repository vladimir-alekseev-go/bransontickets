<?php

use yii\db\Migration;

/**
 * Class m230613_171707_add_categories_sort_fields
 */
class m230613_171707_add_categories_sort_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tr_categories', 'sort_shows', $this->integer(4)->defaultValue(500));
        $this->addColumn('tr_categories', 'sort_attractions', $this->integer(4)->defaultValue(500));
        $this->addColumn('tr_categories', 'sort_hotels', $this->integer(4)->defaultValue(500));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230613_171707_add_categories_sort_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230613_171707_add_categories_sort_fields cannot be reverted.\n";

        return false;
    }
    */
}
