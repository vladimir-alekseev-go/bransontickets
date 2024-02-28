<?php

namespace frontend\controllers;

use common\controllers\SiteControllerTrait;
use Yii;
use yii\authclient\AuthAction;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\db\Expression;
use common\models\TrAttractions;
use common\models\TrShows;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    use SiteControllerTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $showsAllF = TrShows::getActive()
            ->orderBy(new Expression('rand()'))
            ->andWhere("TRIM(photos) <> ''")
            ->limit(3)
            ->all();

        $attractionsAllF = TrAttractions::getActive()
            ->orderBy(new Expression('rand()'))
            ->andWhere("TRIM(photos) <> ''")
            ->limit(3)
            ->all();

        $showsFeatured = array_merge($showsAllF, $attractionsAllF);

        $showsAllR = TrShows::getActive()
            ->orderBy(new Expression('rand()'))
            ->limit(3)
            ->all();

        $attractionsAllR = TrAttractions::getActive()
            ->orderBy(new Expression('rand()'))
            ->limit(3)
            ->all();

        $showsRecommended = array_merge($showsAllR, $attractionsAllR);

        return $this->render('index', compact('showsFeatured', 'showsRecommended'));
    }

    public function actionSearch(): string
    {
        return $this->render('search');
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
