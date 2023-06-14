<?php

use yii\db\Migration;

/**
 * Class m230612_025855_create_site_settings
 */
class m230612_025855_create_site_settings extends Migration
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
        $this->createTable('site_settings', [
            'id' => $this->primaryKey(),
            'recaptcha_site_key' => $this->string(64)->null(),
            'recaptcha_secret' => $this->string(64)->null(),
            'mailchimp_key' => $this->string(64)->null(),
            'mailchimp_list_id' => $this->string(64)->null(),
            'google_api_key' => $this->string(64)->null(),
            'googletagmanager' => $this->string(64)->null(),
            'logo_id' => $this->integer()->null(),
            'logo_mobile_id' => $this->integer()->null(),
            'favicon_id' => $this->integer()->null(),
            'email' => $this->string(64)->null(),
            'phone' => $this->string(64)->null(),
            'font_style' => $this->string(64)->null(),
            'color_brand' => $this->string(22)->notNull()->defaultValue('#2056AF'),
            'color_brand_lighten_30' => $this->string(22)->notNull()->defaultValue('#80A7E8'),
            'color_brand_darken_10' => $this->string(22)->notNull()->defaultValue('#184184'),
            'color_neutral_a' => $this->string(22)->null(),
            'color_neutral_b' => $this->string(22)->null(),
            'site_name' => $this->string(64)->null(),
            'fax' => $this->string(64)->null(),
            'service_email' => $this->string(64)->null(),
            'logo_print_id' => $this->integer()->null(),
            'show_heared_menu' => $this->integer(1)->notNull()->defaultValue(1),
            'view_params' => $this->string(2048)->null(),
            'google_map_key' => $this->string(64)->null(),
            'logo_link' => $this->string(128)->null(),
            'default_date' => $this->dateTime()->null(),
            'created_at' => $this->datetime()->null(),
            'updated_at' => $this->datetime()->null(),
        ], $tableOptions);
        $this->createIndex('idx-site_settings-logo_id', 'site_settings', 'logo_id', true);
        $this->createIndex('idx-site_settings-favicon_id', 'site_settings', 'favicon_id', true);
        $this->createIndex('idx-site_settings-logo_print_id', 'site_settings', 'logo_print_id', true);
        $this->addForeignKey(
            'fk-site_settings-logo_id',
            'site_settings',
            'logo_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
            );
        $this->addForeignKey(
            'fk-site_settings-favicon_id',
            'site_settings',
            'favicon_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
            );
		$this->addForeignKey(
            'fk-site_settings-logo_print_id',
            'site_settings',
            'logo_print_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
            );
        $this->addForeignKey(
            'fk-site_settings-logo_mobile_id',
            'site_settings',
            'logo_mobile_id',
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
        echo "m230612_025855_create_site_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230612_025855_create_site_settings cannot be reverted.\n";

        return false;
    }
    */
}
