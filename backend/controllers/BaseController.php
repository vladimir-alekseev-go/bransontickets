<?php

namespace backend\controllers;

use common\controllers\UploadFileTrait;
use webvimark\modules\UserManagement\components\GhostAccessControl;
use Yii;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BaseController extends Controller
{
    use UploadFileTrait;
    
    /**
     * @var ActiveRecord
     */
    public $modelClass;

    /**
     * @var ActiveRecord
     */
    public $modelSearchClass;

    /**
     * Actions that will be disabled
     *
     * List of available actions:
     *
     * ['index', 'view', 'create', 'update', 'delete', 'toggle-attribute',
     * 'bulk-activate', 'bulk-deactivate', 'bulk-delete', 'grid-sort', 'grid-page-size']
     *
     * @var array
     */
    public $disabledActions = [];

    /**
     * Opposite to $disabledActions. Every action from AdminDefaultController except those will be disabled
     *
     * But if action listed both in $disabledActions and $enableOnlyActions
     * then it will be disabled
     *
     * @var array
     */
    public $enableOnlyActions = [];

    /**
     * List of actions in this controller. Needed fo $enableOnlyActions
     *
     * @var array
     */
    protected $_implementedActions = [
        'index',
        'view',
        'create',
        'update',
        'delete',
        'toggle-attribute',
        'bulk-activate',
        'bulk-deactivate',
        'bulk-delete',
        'grid-sort',
        'grid-page-size'
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => GhostAccessControl::class,
            ],
            'verbs'        => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Sorting items in grid
     */
    public function actionGridSort()
    {
        if (Yii::$app->request->post('sorter')) {
            $sortArray = Yii::$app->request->post('sorter', []);
            $modelClass = $this->modelClass;
            $models = $modelClass::findAll(array_keys($sortArray));

            foreach ($models as $model) {
                $model->sorter = $sortArray[$model->id];
                $model->save(false);
            }
        }
    }

    /**
     * Set page size for grid
     */
    public function actionGridPageSize()
    {
        if (Yii::$app->request->post('grid-page-size')) {
            $cookie = new Cookie(
                [
                    'name'   => '_grid_page_size',
                    'value'  => Yii::$app->request->post('grid-page-size'),
                    'expire' => time() + 86400 * 365, // 1 year
                ]
            );

            Yii::$app->response->cookies->add($cookie);
        }
    }

    /**
     * Define redirect page after update, create, delete, etc
     *
     * @param string       $action
     * @param ActiveRecord $model
     *
     * @return string|array
     */
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'delete':
                return ['index'];
                break;
            case 'create':
            case 'reply':
            case 'update':
                return ['view', 'id' => $model->id];
                break;
            default:
                return ['index'];
        }
    }

    /**
     * Render ajax or usual depends on request
     *
     * @param string $view
     * @param array  $params
     *
     * @return string|Response
     */
    protected function renderIsAjax($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        }

        return $this->render($view, $params);
    }

    /**
     * @inheritdoc
     *
     * @param Action $action
     *
     * @return bool
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if ($this->enableOnlyActions !== []
                && in_array($action->id, $this->_implementedActions, true)
                && !in_array($action->id, $this->enableOnlyActions, true)) {
                throw new NotFoundHttpException('Page not found');
            }

            if (in_array($action->id, $this->disabledActions, true)) {
                throw new NotFoundHttpException('Page not found');
            }

            return true;
        }

        return false;
    }
}
