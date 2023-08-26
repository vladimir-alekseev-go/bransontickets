<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_hotels`.
 */
class m230819_114336_create_tr_hotels_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(
            'tr_hotels',
            [
                'id'                            => $this->primaryKey(),
                'id_external'                   => $this->integer(11)->notNull()->unique(),
                'status'                        => $this->tinyInteger(1)->notNull(),
                'show_in_footer'                => $this->tinyInteger(4)->notNull()->defaultValue(0),
                'name'                          => $this->string(128)->notNull(),
                'description'                   => $this->text()->null(),
                'amenities'                     => $this->string(2048)->null(),
                'property_amenities'            => $this->string(2048)->null(),
                'preview_id'                    => $this->integer(11)->null(),
                'hash_summ'                     => $this->string(64)->notNull(),
                'hash_summ_fast_update'         => $this->string(64)->null(),
                'code'                          => $this->string(128)->notNull(),
                'photos'                        => $this->text()->null(),
                'address'                       => $this->string(128)->null(),
                'city'                          => $this->string(64)->null(),
                'state'                         => $this->string(8)->null(),
                'phone'                         => $this->string(64)->null(),
                'zip_code'                      => $this->string(16)->null(),
                'fax'                           => $this->string(16)->null(),
                'email'                         => $this->string(128)->null(),
                'hotel_rating'                  => $this->decimal(4, 2)->null(),
                'updated_at'                    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'location_lat'                  => $this->string(16)->null(),
                'location_lng'                  => $this->string(16)->null(),
                'amenities_description'         => $this->text()->null(),
                'property_information'          => $this->text()->null(),
                'area_information'              => $this->text()->null(),
                'property_description'          => $this->text()->null(),
                'hotel_policy'                  => $this->text()->null(),
                'deposit_credit_cards_accepted' => $this->text()->null(),
                'room_information'              => $this->text()->null(),
                'driving_directions'            => $this->text()->null(),
                'check_in_instructions'         => $this->text()->null(),
                'location_description'          => $this->text()->null(),
                'room_detail_description'       => $this->text()->null(),
                'sort'                          => $this->integer(11)->null(),
                'cancel_policy_text'            => $this->string(2048)->null(),
                'voucher_procedure'             => $this->string(1024)->null(),
                'rating'                        => $this->integer(11)->notNull()->defaultValue(0),
                'external_service'              => $this->string(16)->null(),
            ],
            $tableOptions
        );

        $this->createIndex(
            'preview_id',
            'tr_hotels',
            'preview_id'
        );

        $this->addForeignKey(
            'tr_hotels_ibfk_1',
            'tr_hotels',
            'preview_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_hotels');
    }
}
