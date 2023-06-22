<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_shows`.
 */
class m230609_103552_create_tr_shows_table extends Migration
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
            'tr_shows',
            [
                'id'                   => $this->primaryKey(),
                'id_external'          => $this->integer(11)->notNull()->unique(),
                'code'                 => $this->string(128)->notNull()->unique(),
                'name'                 => $this->string(128)->notNull(),
                'description'          => $this->text()->null(),
                'address'              => $this->string(128)->null(),
                'city'                 => $this->string(64)->null(),
                'state'                => $this->string(8)->null(),
                'zip_code'             => $this->string(8)->null(),
                'phone'                => $this->string(64)->null(),
                'fax'                  => $this->string(64)->null(),
                'email'                => $this->string(128)->null(),
                'directions'           => $this->text()->null(),
                'status'               => $this->integer(1)->notNull()->defaultValue(0),
                'show_in_footer'       => $this->tinyInteger(4)->notNull()->defaultValue(0),
                'location_external_id' => $this->integer(11)->null(),
                'rank'                 => $this->integer(11)->null(),
                'marketing_level'      => $this->integer(2)->null(),
                'voucher_procedure'    => $this->string(1024)->null(),
                'weekly_schedule'      => $this->integer(1)->null(),
                'on_special_text'      => $this->string(1024)->null(),
                'cast_size'            => $this->string(16)->null(),
                'seats'                => $this->integer(11)->null(),
                'show_length'          => $this->integer(4)->null(),
                'intermissions'        => $this->string(64)->null(),
                'cut_off'              => $this->integer(4)->null(),
                'tax_rate'             => $this->decimal(5, 2)->null(),
                'hash_summ'            => $this->string(32)->null(),
                'photos'               => $this->string(4096)->null(),
                'preview_id'           => $this->integer(11)->null(),
                'image_id'             => $this->integer(11)->null(),
                'seat_map_id'          => $this->integer(11)->null(),
                'display_image'        => $this->tinyInteger(4)->notNull()->defaultValue(0),
                'theatre_id'           => $this->integer(11)->null(),
                'theatre_name'         => $this->string(128)->null(),
                'amenities'            => $this->string(2048)->null(),
                'tags'                 => $this->string(256)->null(),
                'videos'               => $this->string(2048)->null(),
                'min_rate'             => $this->decimal(7, 2)->null(),
                'min_rate_source'      => $this->decimal(7, 2)->null(),
                'cancel_policy_text'   => $this->string(2048)->null(),
                'updated_at'           => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'location_lat'         => $this->string(16)->null(),
                'location_lng'         => $this->string(16)->null(),
                'call_us_to_book'      => $this->integer(1)->null()->defaultValue(0),
                'external_service'     => $this->string(16)->null(),
            ],
            $tableOptions
        );

        $this->createIndex(
            'idx-tr_shows-image_id',
            'tr_shows',
            'image_id'
        );

        $this->addForeignKey(
            'fk-tr_shows-image_id',
            'tr_shows',
            'image_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );

        $this->createIndex(
            'theatre_id',
            'tr_shows',
            'theatre_id'
        );

        $this->addForeignKey(
            'tr_shows_ibfk_1',
            'tr_shows',
            'theatre_id',
            'tr_theaters',
            'id_external',
            'SET NULL',
            'SET NULL'
        );

        $this->createIndex(
            'seat_map_id',
            'tr_shows',
            'seat_map_id'
        );

        $this->addForeignKey(
            'tr_shows_ibfk_2',
            'tr_shows',
            'seat_map_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );

        $this->createIndex(
            'preview_id',
            'tr_shows',
            'preview_id'
        );

        $this->addForeignKey(
            'tr_shows_ibfk_3',
            'tr_shows',
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
        $this->dropTable('tr_shows');
    }
}
