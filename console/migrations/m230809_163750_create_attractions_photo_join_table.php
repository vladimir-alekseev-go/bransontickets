<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attractions_photo_join`.
 */
class m230809_163750_create_attractions_photo_join_table extends Migration
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
            'attractions_photo_join',
            [
                'id'         => $this->primaryKey(),
                'photo_id'   => $this->integer(11)->notNull(),
                'item_id'    => $this->integer(11)->notNull(),
                'preview_id' => $this->integer(11)->null(),
                'activity'   => $this->tinyInteger(4)->notNull()->defaultValue(0),
                'sort'       => $this->integer(11)->notNull()->defaultValue(0),
            ],
            $tableOptions
        );

        $this->createIndex(
            'idx-attractions_p_j-item_id',
            'attractions_photo_join',
            'item_id'
        );

        $this->addForeignKey(
            'fk-attractions_p_j-item_id',
            'attractions_photo_join',
            'item_id',
            'tr_attractions',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-attractions_p_j-photo_id',
            'attractions_photo_join',
            'photo_id'
        );

        $this->addForeignKey(
            'fk-attractions_p_j-photo_id',
            'attractions_photo_join',
            'photo_id',
            'content_files',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-attractions_p_j-preview_id',
            'attractions_photo_join',
            'preview_id'
        );

        $this->addForeignKey(
            'fk-attractions_p_j-preview_id',
            'attractions_photo_join',
            'preview_id',
            'content_files',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('attractions_photo_join');
    }
}
