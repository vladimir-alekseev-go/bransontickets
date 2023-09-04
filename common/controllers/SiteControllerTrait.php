<?php
namespace common\controllers;

use common\models\auth\LoginForm;
use common\models\auth\PasswordResetRequestForm;
use common\models\auth\ResetPasswordForm;
use common\models\auth\SignupForm;
use common\models\User;
use InvalidArgumentException;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

trait SiteControllerTrait
{
    public function successCallback($client)
    {
        User::socialAuth($client);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['profile/index']);
        }
        if (!empty(Yii::$app->request->get('returnUrl'))) {
            Yii::$app->user->returnUrl = Yii::$app->request->get('returnUrl');
        } else {
            Yii::$app->user->returnUrl = Url::to(['profile/index']);
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $returnUrl = Yii::$app->user->returnUrl;
            Yii::$app->user->returnUrl = Url::to('/');
            return $this->redirect($returnUrl);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestpasswordreset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['supportEmail'], Yii::$app->name)) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->redirect(['/site/requestpasswordreset']);
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetpassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * @param null $id
     * @param null $ak
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionConfirm($id = null, $ak = null)
    {
        if (!Yii::$app->user->isGuest || is_array($id) || is_array($ak)) {
            throw new NotFoundHttpException;
        }

        $user = User::findOne(['id' => $id, 'auth_key' => $ak, 'status' => User::STATUS_REGISTER]);

        if (!$user) {
            return $this->render('confirmation-code-not-found');
        }

        $user->status = User::STATUS_ACTIVE;
        $user->withoutTripium = true;
        $user->save();
        Yii::$app->user->login($user);
        Yii::$app->session->setFlash('emailConfirmation', "You have successfully confirmed your email");

        return $this->redirect(['profile/index'], 302);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect("/");
        }
        $model = new SignupForm(['emailFrom' => ['server@' . Yii::$app->params['domainRoot'] => "Ticket Service"]]);
        if ($model->load(Yii::$app->request->post()) && $user = $model->signup()) {
            Yii::$app->session->addFlash('messages', "Please check your email to complete registration");
        }
        return $this->render('signup', ['model' => $model]);
    }
}
