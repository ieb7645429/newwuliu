<?php

namespace frontend\controllers;

use Yii;
use common\models\SmallPrint;
use common\models\SmallPrintSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use mdm\admin\components\MenuHelper;
use common\models\SmallPrintOrder;
use common\models\Driver;
use common\models\DriverConfig;

/**
 * SmallPrintController implements the CRUD actions for SmallPrint model.
 */
class SmallPrintController extends Controller
{
    public $layout_data;
    public $layout_driver_id;
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
     * Lists all SmallPrint models.
     * @return mixed
     */
    public function actionIndex()
    {
        //同城司机，添加左侧司机列表
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        if($role == Yii::$app->params['roleDriverManagerCityWide']){
            $driver = new Driver();
            $driverConfig = new DriverConfig();
            $this->layout_data = $driver->getDriverList(1);//同城司机左侧列表
            $this->layout_driver_id = $driver_id = $driverConfig->getDriverManagerStatus();//左侧选中司机
        }
        
        $searchModel = new SmallPrintSearch();
        $print_time = $this->getPrintTime(Yii::$app->request->get('SmallPrintSearch')['print_time']);
        $dataProvider = $searchModel->search([
                                                'params'=>Yii::$app->request->queryParams,
                                                'print_time'=>$print_time,
                                                'driver_id'=>isset($driver_id)?$driver_id:Yii::$app->user->id,
                                            ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'print_time' => $print_time,
            'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * Displays a single SmallPrint model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        
        $smallOrder = new SmallPrintOrder();
        $model = json_decode($smallOrder::findOne($id)->print_content);
        return $this->render('view', [
            'print_id' => $id,
            'model' => $model,
            'menus' => $this->_getMenus(),
        ]);
    }
    /**
     * 打印小码单
     */
    public function actionAjaxSmallPrint(){
        $print_id = $_POST['print_id'];
        $printOrder = new SmallPrintOrder();
        $model = $printOrder::findOne($print_id);
        $result = ['code'=>200,'data'=>json_decode($model->print_content)];
        return json_encode($result);
    }

    /**
     * Deletes an existing SmallPrint model.
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
     * Finds the SmallPrint model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SmallPrint the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SmallPrint::findOne($id)) !== null) {
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
                'index' => ['menu' => '/small-print/index', 'item' => false],
                'view' => ['menu' => '/small-print/index', 'item' => false],
        );
    
        return $arr[Yii::$app->controller->action->id];
    }
    /**
     * 靳健
     * 添加时间筛选条件
     * @param unknown $time
     * @return unknown|string
     */
    private function getPrintTime($time){
        if(!empty($time)){
            list($start, $end) = explode(' - ', $time);
            $add_time['start'] = strtotime($start);
            $add_time['end'] = strtotime($end)+60*60*24;
            $add_time['date'] = $time;
            return $add_time;
        }
        return '';
    }
}
