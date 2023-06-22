<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tr_theaters`.
 */
class m230609_103156_create_tr_theaters_table extends Migration
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
            'tr_theaters',
            [
                'id'                  => $this->primaryKey(),
                'id_external'         => $this->integer(11)->notNull()->unique(),
                'name'                => $this->string(64)->null(),
                'address1'            => $this->string(128)->null(),
                'address2'            => $this->string(128)->null(),
                'city'                => $this->string(64)->null(),
                'state'               => $this->string(4)->null(),
                'zip_code'            => $this->string(8)->null(),
                'directions'          => $this->string(1024)->null(),
                'status'              => $this->tinyInteger(1)->notNull()->defaultValue(1),
                'image'               => $this->string(256)->null(),
                'contacts_phone'      => $this->string(16)->null(),
                'contacts_email'      => $this->string(256)->null(),
                'contacts_fax'        => $this->string(16)->null(),
                'additional_phone'    => $this->string(16)->null(),
                'hash_summ'           => $this->string(32)->notNull(),
                'updated_at'          => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
                'location_lat'        => $this->string(16)->null(),
                'location_lng'        => $this->string(16)->null(),
                'location_updated_at' => $this->datetime()->null(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tr_theaters');
    }
}
