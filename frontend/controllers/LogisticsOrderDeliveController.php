<?php

namespace frontend\controllers;

use Yii;
use common\models\LogisticsOrder;
use common\models\LogisticsOrderDeliveSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use mdm\admin\components\MenuHelper;
use common\models\Driver;
use common\models\LogisticsOrderSearch;
use yii\helpers\ArrayHelper;

/**
 * LogisticsOrderDeliveController implements the CRUD actions for LogisticsOrder model.
 */
class LogisticsOrderDeliveController extends Controller
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
     * 已封车
     * @return mixed
     */
    public function actionIndex()
    {
    	$res = Yii::$app->request->queryParams;
    	$condition_time_bye = '';
    	$condition_time_by = '';
    	$condition_time = '';
		$status = '';
    	if(!empty($res['LogisticsOrderSearch'])){
    		$condition_time_by = $res['LogisticsOrderSearch']['condition_time_by'];
	    	$condition_time = $res['LogisticsOrderSearch']['condition_time'];
    	}
    	$a = $condition_time_by;
    	if(empty($a)){
    		$condition_time_bye = '3';
    		$status = '50';
    	}
    	if(empty($condition_time_by)){
    		$condition_time_by = '3';
    	}
    	
    	
    	
		$type = 'delive';
        $searchModel = new LogisticsOrderSearch();
        $dataProvider = $searchModel->driverSearch(Yii::$app->request->queryParams,$condition_time_bye,$type,$status);
        $driver = new Driver();
        $driverList = $driver->getDriverDropList();

        if (Yii::$app->request->get('download_type', '0')) {
        	return $this->_downloadApplyExcel($dataProvider);
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        	'menus' => $this->_getMenus(),
        	'driverList' => $driverList,
        	'condition_time_by' => $condition_time_by,
        	'condition_time' => $condition_time,
        	'status'=>$status,
        ]);
    }
    
    /**
     * 已完成
     */
    public function actionIndexwan(){
    	$res = Yii::$app->request->queryParams;
    	$condition_time_bye = '';
    	$condition_time_by = '';
    	$condition_time = '';
		$status = '';
    	if(!empty($res['LogisticsOrderSearch'])){
    		$condition_time_by = $res['LogisticsOrderSearch']['condition_time_by'];
	    	$condition_time = $res['LogisticsOrderSearch']['condition_time'];
    	}
    	$a = $condition_time_by;
    	if(empty($a)){
    		$condition_time_bye = '3';
    		$status = '80';
    	}
    	if(empty($condition_time_by)){
    		$condition_time_by = '3';
    	}
    	
		$type = 'delive';
        $searchModel = new LogisticsOrderSearch();
        $dataProvider = $searchModel->driverSearch(Yii::$app->request->queryParams,$condition_time_bye,$type,$status);
        $driver = new Driver();
        $driverList = $driver->getDriverDropList();
        
        if (Yii::$app->request->get('download_type', '0')) {
        	return $this->_downloadApplyExcel($dataProvider);
        }
        
        return $this->render('indexwan', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        	'menus' => $this->_getMenus(),
        	'driverList' => $driverList,
        	'condition_time_by' => $condition_time_by,
        	'condition_time' => $condition_time,
        	'status'=>$status,
        ]);
    }

    /**
     * 配送信息明细
     * 齐经纬
     * 2017-11-08
     */
    public function actionView1(){
		$searchModel = new LogisticsOrderSearch();
		$dataProvider = $searchModel->driverSearch(Yii::$app->request->queryParams);
		$driver = new Driver();
		$driverList = $driver->getDriverDropList();

		return $this->render('index1', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'menus' => $this->_getMenus(),
			'driverList' => $driverList
		]);
    }
    
    /**
     * Displays a single LogisticsOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LogisticsOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LogisticsOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->order_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LogisticsOrder model.
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
     * Deletes an existing LogisticsOrder model.
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
     * Finds the LogisticsOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LogisticsOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LogisticsOrder::findOne($id)) !== null) {
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
    			'index' => ['menu' => '/logistics-order-delive/index', 'item' => '/logistics-order-delive/index'],
    			'view' => ['menu' => '/logistics-order-delive/index', 'item' => false],
    			'view1' => ['menu' => '/logistics-order-delive/index','item' => false],
    			'indexwan' => ['menu' => '/logistics-order-delive/index','item' => '/logistics-order-delive/indexwan'],
    	);
    
    	return $arr[Yii::$app->controller->action->id];
    }
    
    /**
     * 导出
     * @param  ahthor:齐经纬
     * @param  time：2017-11-15
     */
    private function _downloadApplyExcel($dataProvider) {
    	// Create new PHPExcel object
    	$objPHPExcel = new \PHPExcel();
    
    	// Set document properties
    	$objPHPExcel->getProperties()
    	->setCreator("wuliu.youjian8.com")
    	->setLastModifiedBy("wuliu.youjian8.com")
    	->setTitle("youjian logistics order")
    	->setSubject("youjian logistics order")
    	->setDescription("youjian logistics order")
    	->setKeywords("youjian logistics order")
    	->setCategory("youjian logistics order");
    	$datas = $dataProvider->query->all();
    	if ($datas) {
    		$objPHPExcel->setActiveSheetIndex(0)
    		->setCellValue('A1', '编号')
    		->setCellValue('B1', '司机')
    		->setCellValue('C1', '配送票数');
    		$i = 2;
    		foreach ($datas as $model) {
    			// Add some data
    			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, ArrayHelper::getValue($model, 'driverUserName.username'));
    			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, ArrayHelper::getValue($model, 'driverTrueName.user_truename'));
    			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, ArrayHelper::getValue($model, 'countnum'));
    			$i++;
    		}
    	}
    
    	// Rename worksheet
    	$objPHPExcel->getActiveSheet()->setTitle('配送信息管理');
    
    
    	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    	$objPHPExcel->setActiveSheetIndex(0);
    
    
    	// Redirect output to a client’s web browser (Excel5)
    	header('Content-Type: application/vnd.ms-excel');
    	header('Content-Disposition: attachment;filename="配送信息管理.xls"');
    	header('Cache-Control: max-age=0');
    	// If you're serving to IE 9, then the following may be needed
    	header('Cache-Control: max-age=1');
    
    	// If you're serving to IE over SSL, then the following may be needed
    	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    	header ('Pragma: public'); // HTTP/1.0
    
    	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    	$objWriter->save('php://output');
    	exit;
    }
    
}
