<?php

use yii\db\Migration;

/**
 * Class m240216_143140_rename_fields_rank
 */
class m240216_143140_rename_fields_rank extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->renameColumn('tr_shows', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
        try {
            $this->renameColumn('tr_prices', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
        try {
            $this->renameColumn('tr_attractions', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
        try {
            $this->renameColumn('tr_attractions_prices', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
        try {
            $this->renameColumn('tr_pos_hotels', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
        try {
            $this->renameColumn('tr_pos_pl_hotels', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
        try {
            $this->renameColumn('tr_pos_hotels_price_room', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
        try {
            $this->renameColumn('tr_pos_hotels_price_extra', 'rank', 'rank_level');
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240216_143140_rename_fields_rank cannot be reverted.\n";

        return false;
    }
}
