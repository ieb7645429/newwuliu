<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrderDelete;
use common\models\LogisticsOrderDeleteSearch;
use yii\web\Controller;
use mdm\admin\components\MenuHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\LogisticsOrder;

/**
 * OrderDeleteController implements the CRUD actions for LogisticsOrderDelete model.
 */
class OrderDeleteController extends Controller
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
     * Lists all LogisticsOrderDelete models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LogisticsOrderDeleteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * Displays a single LogisticsOrderDelete model.
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
     * Creates a new LogisticsOrderDelete model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LogisticsOrderDelete();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->order_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LogisticsOrderDelete model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->order_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LogisticsOrderDelete model.
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
     * 取消删除
     * 齐经纬
     * 2017-11-02
     */
    public function actionNodel($id)
    {
    	$model = $this->findModel($id);
    	$ordermodel = new LogisticsOrder();
    	
    	$ordermodel->order_id = $model->order_id;
    	$ordermodel->logistics_sn = $model->logistics_sn;
    	$ordermodel->goods_sn = $model->goods_sn;
    	$ordermodel->order_sn = $model->order_sn;
    	$ordermodel->freight = $model->freight;
    	$ordermodel->goods_price = $model->goods_price;
    	$ordermodel->make_from_price = $model->make_from_price;
    	$ordermodel->goods_num = $model->goods_num;
    	$ordermodel->order_state = $model->order_state;
    	$ordermodel->state = $model->state;
    	$ordermodel->freight_state = $model->freight_state;
    	$ordermodel->goods_price_state = $model->goods_price_state;
    	$ordermodel->abnormal = $model->abnormal;
    	$ordermodel->collection = $model->collection;
    	$ordermodel->collection_poundage_one = $model->collection_poundage_one;
    	$ordermodel->collection_poundage_two = $model->collection_poundage_two;
    	$ordermodel->order_type = $model->order_type;
    	$ordermodel->add_time = $model->add_time;
    	$ordermodel->member_name = $model->member_name;
    	$ordermodel->member_id = $model->member_id;
    	$ordermodel->member_cityid = $model->member_cityid;
    	$ordermodel->member_phone = $model->member_phone;
    	$ordermodel->receiving_name = $model->receiving_name;
    	$ordermodel->receiving_phone = $model->receiving_phone;
    	$ordermodel->receiving_name_area = $model->receiving_name_area;
    	$ordermodel->receiving_provinceid = $model->receiving_provinceid;
    	$ordermodel->receiving_cityid = $model->receiving_cityid;
    	$ordermodel->receiving_areaid = $model->receiving_areaid;
    	$ordermodel->terminus_id = $model->terminus_id;
    	$ordermodel->logistics_route_id = $model->logistics_route_id;
    	$ordermodel->shipping_type = $model->shipping_type;
    	$ordermodel->employee_id = $model->employee_id;
    	$ordermodel->driver_member_id = $model->driver_member_id;
    	$ordermodel->test = $model->test;
    	$ordermodel->shipping_sale = $model->shipping_sale;
    	$ordermodel->scale = $model->scale;
    	$ordermodel->same_city = $model->same_city;
    	$ordermodel->return_logistics_sn = $model->return_logistics_sn;
    	$ordermodel->buy_confirm = (int)0;
    	
    	$ordermodel->save();
    	
    	$this->findModel($id)->delete();
    	
    	return $this->redirect(['index']);

    }

    /**
     * Finds the LogisticsOrderDelete model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogisticsOrderDelete the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticsOrderDelete::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
                'index' => ['menu' => '/order-delete/index', 'item' => '/order-delete/index'],
                'view' => ['menu' => '/order-delete/index', 'item' => false],
        );
    
        return $arr[Yii::$app->controller->action->id];
    }
}
