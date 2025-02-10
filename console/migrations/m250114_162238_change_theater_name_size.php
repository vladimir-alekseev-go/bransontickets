<?php

use yii\db\Migration;

/**
 * Class m250114_162238_change_theater_name_size
 */
class m250114_162238_change_theater_name_size extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->alterColumn('tr_theaters', 'name', $this->string(128)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m250114_162238_change_theater_name_size cannot be reverted.\n";

        return false;
    }
}
