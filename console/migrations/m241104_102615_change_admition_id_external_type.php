<?php

use common\helpers\DB;
use common\models\VacationPackage;
use yii\db\Migration;

/**
 * Class m241104_102615_change_admition_id_external_type
 */
class m241104_102615_change_admition_id_external_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        /** @var VacationPackage[] $vps */
        $vps = VacationPackage::find()->all();
        foreach ($vps as $vp) {
            $vp->delete();
        }

        $db = Yii::$app->getDb();
        $db->createCommand(
            "DELETE FROM vacation_package_attraction where id > 0"
        )->execute();
        $db->createCommand(
            "DELETE FROM vacation_package_category where id > 0"
        )->execute();
        $db->createCommand(
            "DELETE FROM vacation_package_price where id > 0"
        )->execute();
        $db->createCommand(
            "DELETE FROM vacation_package_show where id > 0"
        )->execute();

        $fk = DB::getFK('tr_attractions_prices', 'tr_admissions');
        if ($fk) {
            $this->dropForeignKey($fk, 'tr_attractions_prices');
        }
        $fk = DB::getFK('tr_attractions_availability', 'tr_admissions');
        if ($fk) {
            $this->dropForeignKey($fk, 'tr_attractions_availability');
        }
        $fk = DB::getFK('vacation_package_attraction', 'tr_admissions');
        if ($fk) {
            $this->dropForeignKey($fk, 'vacation_package_attraction');
        }

        $this->alterColumn('tr_admissions', 'id_external', $this->string(36)->notNull());
        $this->alterColumn('tr_attractions_prices', 'id_external', $this->string(36)->notNull());
        $this->alterColumn('tr_attractions_availability', 'id_external', $this->string(36)->notNull());
        $this->alterColumn('vacation_package_attraction', 'item_type_id', $this->string(36)->notNull());

        $this->addforeignKey(
            'fk-tr_attractions_prices-id_external',
            'tr_attractions_prices',
            'id_external',
            'tr_admissions',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
        $this->addforeignKey(
            'fk-tr_attractions_availability-id_external',
            'tr_attractions_availability',
            'id_external',
            'tr_admissions',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
        $this->addforeignKey(
            'fk-vpa-item_type_id',
            'vacation_package_attraction',
            'item_type_id',
            'tr_admissions',
            'id_external',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m241104_102615_change_admition_id_external_type cannot be reverted.\n";

        return false;
    }
}
