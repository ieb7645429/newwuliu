<?php

namespace frontend\modules\hlj\controllers;

use Yii;
use frontend\modules\hlj\models\LogisticsOrderEdit;
use frontend\modules\hlj\models\LogisticsOrderEditSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use mdm\admin\components\MenuHelper;
use yii\filters\VerbFilter;

/**
 * LogisticsOrderEditController implements the CRUD actions for LogisticsOrderEdit model.
 */
class LogisticsOrderEditController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LogisticsOrderEdit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LogisticsOrderEditSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $type = '';
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
            'indexOver'=>$type,
        ]);
    }

    /**
     * Displays a single LogisticsOrderEdit model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * Creates a new LogisticsOrderEdit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LogisticsOrderEdit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LogisticsOrderEdit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LogisticsOrderEdit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 取得menus
     * @return array[]|string[]
     */
    private function _getMenus()
    {
        $menus = MenuHelper::getAssignedMenu(Yii::$app->user->id);
        $items = array();

        $activeMenus = $this->_getActiveMenu();

        foreach ($menus as &$menu) {
            if ($menu['url'][0] == $activeMenus['menu']) {
                $menu['active'] = 'active';
                if($activeMenus['item'] !== false && isset($menu['items'])) {
                    foreach ($menu['items'] as &$item) {
                        if($item['url'][0] == $activeMenus['item']) {
                            $item['active'] = 'active';
                            break;
                        }
                    }
                    $items = $menu['items'];
                }
            }
        }
        return ['menus' => $menus, 'items' => $items];
    }

    private function _getActiveMenu() {
        $arr = array(
            'index' => ['menu' => '/hlj/logistics-order-edit/index', 'item' => 'hlj/logistics-order-edit/index'],
            'view' => ['menu' => '/hlj/logistics-order-edit/index', 'item' => false],
//            'index-over' => ['menu' => '/employee/index', 'item' => '/employee/index-over'],
//            'create' => ['menu' => '/employee/index', 'item' => false],
//            'pay' => ['menu' => '/employee/pay', 'item' => false],
        );
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        if($role==Yii::$app->params['roleEmployeeDelete']){
            $arr['update'] = ['menu' => '/hlj/balance-edit/index', 'item' => false];
        }else{
            $arr['update'] = ['menu' => '/hlj/logistics-order-edit/index', 'item' => false];
        }

        return $arr[Yii::$app->controller->action->id];
    }

    /**
     * Finds the LogisticsOrderEdit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogisticsOrderEdit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticsOrderEdit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
