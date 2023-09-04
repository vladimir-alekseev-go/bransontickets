<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m230904_123029_create_users_table extends Migration
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
            'users',
            [
                'id'                   => $this->primaryKey(),
                'status'               => $this->tinyInteger(4)->notNull(),
                'username'             => $this->string(64)->null()->unique(),
                'first_name'           => $this->string(64)->null(),
                'last_name'            => $this->string(64)->null(),
                'email'                => $this->string(64)->null()->unique(),
                'fb_id'                => $this->bigInteger(20)->null(),
                'tw_id'                => $this->bigInteger(20)->null(),
                'gp_id'                => $this->string(32)->null(),
                'tripium_id'           => $this->integer(11)->null(),
                'created_at'           => $this->datetime()->notNull(),
                'updated_at'           => $this->datetime()->notNull(),
                'logined_at'           => $this->datetime()->notNull(),
                'auth_key'             => $this->string(64)->null(),
                'password_hash'        => $this->string(64)->null(),
                'password_reset_token' => $this->string(64)->null(),
                'phone'                => $this->string(32)->null(),
                'address'              => $this->string(128)->null(),
                'city'                 => $this->string(64)->null(),
                'zip_code'             => $this->string(8)->null(),
                'state'                => $this->string(32)->null(),
            ],
            $tableOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }
}
