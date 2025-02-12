<?php

use yii\db\Migration;

/**
 * Class m250210_045953_add_unique_status_to_fields
 */
class m250210_045953_add_unique_status_to_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tr_admissions', 'id_external', $this->string(36)->notNull()->unique());
        $this->alterColumn('tr_categories', 'id_external', $this->integer()->notNull()->unique());
        $this->alterColumn('tr_pos_room_types', 'id_external', $this->string(36)->notNull()->unique());
        $this->alterColumn('vacation_package', 'vp_external_id', $this->integer()->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
