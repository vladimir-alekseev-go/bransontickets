<?php

use yii\db\Migration;

/**
 * Class m230830_081151_change_tr_theartes_external_id
 */
class m230830_081151_change_tr_theartes_external_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->dropForeignKey('fk-tr_pos_pl_hotels-seat_location_theatre_id', 'tr_pos_pl_hotels');
        } catch (Exception $e) {
        }

        try {
            $this->dropForeignKey('tr_attractions_ibfk_1', 'tr_attractions');
        } catch (Exception $e) {
        }

        try {
            $this->dropForeignKey('tr_lunchs_ibfk_1', 'tr_lunchs');
        } catch (Exception $e) {
        }

        try {
            $this->dropForeignKey('tr_shows_ibfk_1', 'tr_shows');
        } catch (Exception $e) {
        }

        $this->alterColumn('tr_theaters', 'id_external', $this->bigInteger(20)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230830_081151_change_tr_theartes_external_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_081151_change_tr_theartes_external_id cannot be reverted.\n";

        return false;
    }
    */
}
