<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_pos_pl_hotels`.
 */
class m230819_143204_create_tr_pos_pl_hotels_table extends Migration
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
        $this->createTable('tr_pos_pl_hotels', [
            'id'                   => $this->primaryKey(),
            'id_external'          => $this->integer()->notNull(),
            'code'                 => $this->string(128)->notNull(),
            'name'                 => $this->string(128)->notNull(),
            'description'          => $this->text(),
            'address'              => $this->string(128)->null(),
            'city'                 => $this->string(164)->null(),
            'state'                => $this->string(8)->null(),
            'zip_code'             => $this->string(8)->null(),
            'phone'                => $this->string(64)->null(),
            'fax'                  => $this->string(64)->null(),
            'email'                => $this->string(128)->null(),
            'directions'           => $this->text(),
            'status'               => $this->integer(1)->notNull()->defaultValue(0),
            'show_in_footer'       => $this->integer(1)->notNull()->defaultValue(0),
            'location_external_id' => $this->integer()->null(),
            'rank_level'           => $this->integer()->null(),
            'marketing_level'      => $this->integer(2)->null(),
            'weekly_schedule'      => $this->integer(1)->null(),
            'voucher_procedure'    => $this->string(1024)->null(),
            'on_special_text'      => $this->string(1024)->null(),
            'tags'                 => $this->string(256)->null(),
            'photos'               => $this->string(2048)->null(),
            'videos'               => $this->string(2048)->null(),
            'amenities'            => $this->string(2048)->null(),
            'cancel_policy_text'   => $this->string(2048)->null(),
            'location_lat'         => $this->string(16)->null(),
            'location_lng'         => $this->string(16)->null(),
            'external_service'     => $this->string(16)->null(),
            'call_us_to_book'      => $this->integer(1)->null(),
            'preview_id'           => $this->integer()->null(),
            'image_id'             => $this->integer()->null(),
            'display_image'        => $this->integer(1)->notNull()->defaultValue(0),
            'theatre_id'           => $this->integer()->null(),
            'min_rate'             => $this->decimal(7, 2)->null(),
            'min_rate_source'      => $this->decimal(7, 2)->null(),
            'hash_summ'            => $this->string(32)->notNull(),
            'hash_image_content'   => $this->string(32)->null(),
            'rating'               => $this->decimal(2, 1)->null(),
            'review_rating'        => $this->decimal(2, 1)->null(),
            'review_rating_desc'   => $this->string(16)->null(),
            'update_preview'       => $this->tinyInteger(1)->null()->defaultValue(1),
            'check_in'             => $this->string(8)->null(),
            'check_out'            => $this->string(8)->null(),
            'updated_at'           => $this->datetime()->null(),
        ], $tableOptions);
        $this->createIndex('idx-tr_pos_pl_hotels-id_external', 'tr_pos_pl_hotels', 'id_external', true);
        $this->createIndex('idx-tr_pos_pl_hotels-code', 'tr_pos_pl_hotels', 'code', true);
        $this->createIndex('idx-tr_pos_pl_hotels-preview_id', 'tr_pos_pl_hotels', 'preview_id');
        $this->createIndex('idx-tr_pos_pl_hotels-image_id', 'tr_pos_pl_hotels', 'image_id');
        $this->addForeignKey(
            'fk-tr_pos_pl_hotels-seat_preview_id',
            'tr_pos_pl_hotels',
            'preview_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk-tr_pos_pl_hotels-seat_image_id',
            'tr_pos_pl_hotels',
            'image_id',
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
        $this->dropTable('tr_pos_pl_hotels');
    }
}
