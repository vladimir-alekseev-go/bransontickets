<?php

namespace frontend\controllers;

use common\controllers\SiteControllerTrait;
use DateInterval;
use DateTime;
use frontend\components\CustomerErrorAction;
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
            'error'   => [
                'class' => CustomerErrorAction::class,
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
        $queryShows = TrShows::getAvailable()->with(['theatre', 'preview'])
            ->andWhere(['<', 'start', (new DateTime)->add(new DateInterval('P30D'))->format('Y-m-d H:i:s')])
            ->andWhere(['not', ['min_rate' => null]])
            ->groupBy(TrShows::tableName() . '.id')
            ->orderBy(new Expression('rand()'))
            ->andWhere("TRIM(photos) <> ''")
            ->limit(3);
        $queryAttractions = TrAttractions::getAvailable()->with(['theatre', 'preview'])
            ->andWhere(['<', 'start', (new DateTime)->add(new DateInterval('P30D'))->format('Y-m-d H:i:s')])
            ->andWhere(['not', ['min_rate' => null]])
            ->groupBy(TrAttractions::tableName() . '.id')
            ->orderBy(new Expression('rand()'))
            ->andWhere("TRIM(photos) <> ''")
            ->limit(3);

        $showsAllF = $queryShows->all();
        $showsAllR = $queryShows->all();
        $attractionsAllF = $queryAttractions->all();
        $attractionsAllR = $queryAttractions->all();

        $showsFeatured = array_merge($showsAllF, $attractionsAllF);
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
    public function actionAboutOld()
    {
        return $this->render('about');
    }
}
