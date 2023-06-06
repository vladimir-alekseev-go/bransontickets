<?php

use webvimark\modules\UserManagement\models\User;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $user = User::find()->one();
        if (!$user) {
            throw new RuntimeException("Should to run `php yii migrate --migrationPath=vendor/webvimark/module-user-management/migrations/`");
        }
    }

    public function down()
    {
        return false;
    }
}
