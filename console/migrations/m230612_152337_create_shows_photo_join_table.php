<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shows_photo_join`.
 */
class m230612_152337_create_shows_photo_join_table extends Migration
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
            'shows_photo_join',
            [
                'id'         => $this->primaryKey(),
                'photo_id'   => $this->integer(11)->notNull(),
                'item_id'    => $this->integer(11)->notNull(),
                'preview_id' => $this->integer(11)->null(),
                'activity'   => $this->tinyInteger(4)->notNull()->defaultValue(0),
                'activity'   => $this->integer(11)->notNull()->defaultValue(0),
            ],
            $tableOptions
        );

        $this->createIndex(
            'idx-shows_photo_join-item_id',
            'shows_photo_join',
            'item_id'
        );

        $this->addForeignKey(
            'fk-shows_photo_join-item_id',
            'shows_photo_join',
            'item_id',
            'tr_shows',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-shows_photo_join-photo_id',
            'shows_photo_join',
            'photo_id'
        );

        $this->addForeignKey(
            'fk-shows_photo_join-photo_id',
            'shows_photo_join',
            'photo_id',
            'content_files',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-shows_photo_join-preview_id',
            'shows_photo_join',
            'preview_id'
        );

        $this->addForeignKey(
            'fk-shows_photo_join-preview_id',
            'shows_photo_join',
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
        $this->dropTable('shows_photo_join');
    }
}
