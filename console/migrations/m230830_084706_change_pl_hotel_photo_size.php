<?php

use yii\db\Migration;

/**
 * Class m230830_084706_change_pl_hotel_photo_size
 */
class m230830_084706_change_pl_hotel_photo_size extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tr_pos_pl_hotels', 'photos', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230830_084706_change_pl_hotel_photo_size cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_084706_change_pl_hotel_photo_size cannot be reverted.\n";

        return false;
    }
    */
}
