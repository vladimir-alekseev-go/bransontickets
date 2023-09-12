<?php

use yii\db\Migration;

/**
 * Class m230911_131237_create_vacation_packages
 */
class m230911_131237_create_vacation_packages extends Migration
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
        $this->createTable('vacation_package', [
            'id'             => $this->primaryKey(),
            'vp_external_id' => $this->integer()->notNull(),
            'name'           => $this->string(128)->notNull(),
            'code'           => $this->string(128)->null(),
            'description'    => $this->string(4096)->null(),
            'status'         => $this->integer(1)->notNull(),
            'period_start'   => $this->datetime()->notNull(),
            'period_end'     => $this->datetime()->notNull(),
            'valid_start'    => $this->datetime()->notNull(),
            'valid_end'      => $this->datetime()->notNull(),
            'hash'           => $this->string(32)->null(),
            'data'           => $this->text()->null(),
            'preview_id'     => $this->integer()->null(),
            'image_id'       => $this->integer()->null(),
            'channel'        => $this->string(32)->null(),
            'created_at'     => $this->datetime()->null(),
            'updated_at'     => $this->datetime()->null(),
        ], $tableOptions);
        $this->createIndex('idx-vp-vp_external_id', 'vacation_package', 'vp_external_id');
        
        $this->createTable('vacation_package_show', [
            'id'               => $this->primaryKey(),
            'vp_external_id'   => $this->integer()->notNull(),
            'item_external_id' => $this->integer()->notNull(),
            'item_type_id'     => $this->integer()->null(),
        ], $tableOptions);
        $this->createIndex('idx-vps-vp_external_id', 'vacation_package_show', 'vp_external_id');
        $this->createIndex('idx-vps-vp_item_external_id', 'vacation_package_show', 'item_external_id');
        
        $this->createTable('vacation_package_attraction', [
            'id'               => $this->primaryKey(),
            'vp_external_id'   => $this->integer()->notNull(),
            'item_external_id' => $this->integer()->notNull(),
            'item_type_id'     => $this->integer()->null(),
        ], $tableOptions);
        $this->createIndex('idx-vpa-vp_external_id', 'vacation_package_attraction', 'vp_external_id');
        $this->createIndex('idx-vpa-vp_item_external_id', 'vacation_package_attraction', 'item_external_id');

        $this->createIndex(
            'idx-vpa-item_type_id',
            'vacation_package_attraction',
            'item_type_id'
        );
        
        $this->addForeignKey(
            'fk-vpa-item_type_id',
            'vacation_package_attraction',
            'item_type_id',
            'tr_admissions',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
        
        $this->createTable('vacation_package_price', [
            'id'             => $this->primaryKey(),
            'vp_external_id' => $this->integer()->notNull(),
            'price'          => $this->decimal(8,2)->notNull(),
            'count'          => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-vpp-vp_external_id', 'vacation_package_price', 'vp_external_id');
        
        $this->createTable('vacation_package_category', [
            'id'             => $this->primaryKey(),
            'vp_external_id' => $this->integer()->notNull(),
            'name'           => $this->string(128)->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-vpc-vp_external_id', 'vacation_package_category', 'vp_external_id');
        
        $this->addForeignKey(
            'fk-vps-vp_external_id', 
            'vacation_package_show', 
            'vp_external_id', 
            'vacation_package', 
            'vp_external_id', 
            'CASCADE', 
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-vps-item_external_id', 
            'vacation_package_show', 
            'item_external_id', 
            'tr_shows', 
            'id_external', 
            'CASCADE', 
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-vpa-vp_external_id', 
            'vacation_package_attraction', 
            'vp_external_id', 
            'vacation_package', 
            'vp_external_id', 
            'CASCADE', 
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-vpa-item_external_id', 
            'vacation_package_attraction', 
            'item_external_id', 
            'tr_attractions', 
            'id_external', 
            'CASCADE', 
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-vpp-vp_external_id', 
            'vacation_package_price', 
            'vp_external_id', 
            'vacation_package', 
            'vp_external_id', 
            'CASCADE', 
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-vpc-vp_external_id', 
            'vacation_package_category', 
            'vp_external_id', 
            'vacation_package', 
            'vp_external_id', 
            'CASCADE', 
            'CASCADE'
        );

        $this->createIndex(
            'idx-vacation_package-preview_id',
            'vacation_package',
            'preview_id'
        );
        
        $this->addForeignKey(
            'fk-vacation_package-preview_id',
            'vacation_package',
            'preview_id',
            'content_files',
            'id',
            'SET NULL',
            'SET NULL'
        );

        $this->createIndex(
            'idx-vacation_package-image_id',
            'vacation_package',
            'image_id'
        );
                
        $this->addForeignKey(
            'fk-vacation_package-image_id',
            'vacation_package',
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
        $this->dropTable('vacation_package_show');
        $this->dropTable('vacation_package_attraction');
        $this->dropTable('vacation_package_price');
        $this->dropTable('vacation_package_category');
        $this->dropTable('vacation_package');
    }
}
