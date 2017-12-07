<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrder;
use common\models\LogisticsOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use mdm\admin\components\MenuHelper;
use yii\data\Pagination;


/**
 * 订单统计数量
 */
class StatisticasController extends Controller
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
     * Lists all LogisticsOrder models.
     * @return mixed
	 * 收货人返货统计
     */
    public function actionRece()
    {   
        $searchModel  = new LogisticsOrderSearch();
        $dataProvider = $searchModel->search_new(Yii::$app->request->queryParams,'rece');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }
	 /**
     * Lists all LogisticsOrder models.
     * @return mixed
	 * 发货人返货统计
     */
    public function actionSend()
    {   
        $searchModel  = new LogisticsOrderSearch();
        $dataProvider = $searchModel->search_new(Yii::$app->request->queryParams,'send');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
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
            'rece' => ['menu' => '/statisticas/rece', 'item' => false],
			'send' => ['menu' => '/statisticas/send', 'item' => false],
        );

        return $arr[Yii::$app->controller->action->id];
    }
}

