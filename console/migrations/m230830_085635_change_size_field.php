<?php

use yii\db\Migration;

/**
 * Class m230830_085635_change_size_field
 */
class m230830_085635_change_size_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tr_pos_pl_hotels', 'theatre_id', $this->bigInteger(20)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230830_085635_change_size_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_085635_change_size_field cannot be reverted.\n";

        return false;
    }
    */
}
