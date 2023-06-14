<?php

use yii\db\Migration;

/**
 * Class m230614_002917_add_location_updated_at_to_theaters
 */
class m230614_002917_add_location_updated_at_to_theaters extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tr_theaters', 'location_updated_at', $this->datetime()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_002917_add_location_updated_at_to_theaters cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_002917_add_location_updated_at_to_theaters cannot be reverted.\n";

        return false;
    }
    */
}
