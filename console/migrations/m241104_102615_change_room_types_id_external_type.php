<?php

use common\helpers\DB;
use yii\db\Migration;

/**
 * Class m241104_102615_change_room_types_id_external_type
 */
class m241104_102615_change_room_types_id_external_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $fk = DB::getFK('tr_pos_hotels_price_extra', 'tr_pos_room_types');
        if ($fk) {
            $this->dropForeignKey($fk, 'tr_pos_hotels_price_extra');
        }
        $fk = DB::getFK('tr_pos_hotels_price_room', 'tr_pos_room_types');
        if ($fk) {
            $this->dropForeignKey($fk, 'tr_pos_hotels_price_room');
        }
        $fk = DB::getFK('tr_pos_hotels_photo_join', 'tr_pos_room_types');
        if ($fk) {
            $this->dropForeignKey($fk, 'tr_pos_hotels_photo_join');
        }

        $this->alterColumn('tr_pos_room_types', 'id_external', $this->string(36)->notNull());
        $this->alterColumn('tr_pos_hotels_price_extra', 'id_external', $this->string(36)->notNull());
        $this->alterColumn('tr_pos_hotels_price_room', 'id_external', $this->string(36)->notNull());
        $this->alterColumn('tr_pos_hotels_photo_join', 'room_type_external_id', $this->string(36)->null());

        $this->addforeignKey(
            'fk-tphpe-id_external',
            'tr_pos_hotels_price_extra',
            'id_external',
            'tr_pos_room_types',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
        $this->addforeignKey(
            'fk-tphpr-id_external',
            'tr_pos_hotels_price_room',
            'id_external',
            'tr_pos_room_types',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
        $this->addforeignKey(
            'fk-tphpj-room_type_external_id',
            'tr_pos_hotels_photo_join',
            'room_type_external_id',
            'tr_pos_room_types',
            'id_external',
            'SET NULL',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m241104_102615_change_room_types_id_external_type cannot be reverted.\n";

        return false;
    }
}
