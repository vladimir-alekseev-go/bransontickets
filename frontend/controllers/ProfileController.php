<?php

namespace frontend\controllers;

use common\models\auth\ChangePasswordForm;
use common\models\TrOrders;
use common\models\User;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Site controller
 */
class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->identity->isProfileComplete()) {
            Yii::$app->session->setFlash('warnings', 'Please fill in the required fields');
            return $this->redirect(['profile/edit'], 302);
        }

        $user = User::getCurrentUser();

        if (!$user) {
            return $this->redirect('/');
        }

        $User = new User;
        $User->updateFromTripium($user->id, true);

        $user = User::getCurrentUser(true);

        $orders = TrOrders::find()
            ->where(['tripium_user_id' => $user->tripium_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('profile', compact('user', 'orders'));
    }

    public function actionEdit()
    {
        $model = User::getCurrentUser();

        if (!$model) {
            return $this->redirect('/');
        }

        $model->scenario = 'profile';

        if ($model->load(Yii::$app->request->post())) {
            if ($model->getDirtyAttributes() && $model->save()) {
                Yii::$app->session->setFlash('userSaveSuccess', 'Data saved');
                $redirect_url = Yii::$app->getUser()->getReturnUrl('');
                if ($redirect_url) {
                    Yii::$app->getUser()->setReturnUrl('');
                    Yii::$app->session->getFlash('userSaveSuccess');
                    return $this->redirect($redirect_url);
                }
            }
        } else {
            $model->validate();
        }

        return $this->render('profile-edit', compact('model'));
    }

    public function actionChangePassword()
    {
        $model = User::getCurrentUser();

        if (!$model) {
            return $this->redirect('/');
        }

        $model->scenario = 'profile';

        $changePasswordForm = new ChangePasswordForm;

        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->changePassword()) {
            Yii::$app->session->setFlash('changePasswordSuccess', 'Password changed');
            return $this->redirect(['profile/change-password']);
        }

        return $this->render('profile-change-password', compact('model', 'changePasswordForm'));
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['profile/index']);
        }

        if (Yii::$app->request->post()) {
            /**
             * @var User $identity
             */
            $identity = Yii::$app->user->identity;

            Yii::$app->user->logout();

            $identity->delete();

            return $this->render('deleted');
        }

        return $this->redirect(['profile/index']);
    }
}
