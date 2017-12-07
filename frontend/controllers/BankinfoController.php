<?php

namespace frontend\controllers;

use Yii;
use common\models\BankInfo;
use common\models\BankInfoSearch;
use common\models\TerminusUser;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use mdm\admin\components\MenuHelper;

/**
 * BankinfoController implements the CRUD actions for Bankinfo model.
 */
class BankinfoController extends Controller
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
     * Lists all Bankinfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankInfoSearch();
		//获取相同落地点的值的信息
        $Id = $this->GetTerminusUser();
        if($Id){
          if (in_array(Yii::$app->params['roleTerminus'], array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))))
	      {
			  Yii::$app->request->queryParams=array_merge(Yii::$app->request->queryParams,array('BankInfoSearch'=>array('user_id'=>'','bank_info_id'=>'','bank_info_card_no'=>'','bank_info_account_name'=>'','bank_info_bank_name'=>'','bank_info_place'=>'luodi_'.$Id)));
	       }
		}
		else{
		 Yii::$app->request->queryParams=array_merge(Yii::$app->request->queryParams,array('BankInfoSearch'=>array('user_id'=>Yii::$app->user->id,'bank_info_id'=>'','bank_info_card_no'=>'','bank_info_account_name'=>'','bank_info_bank_name'=>'')));
		}

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * Displays a single Bankinfo model.
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
     * Creates a new Bankinfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model  = new Bankinfo();
		$source = 'normal_'.Yii::$app->user->id;
       //判断来源是否是落地
	   if (in_array(Yii::$app->params['roleTerminus'], array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))))
	   {
		 //获取落地点Id
		 $Id = $this->GetTerminusUser();
		 $source = 'luodi_'.$Id;
	   }
        if ($model->load(Yii::$app->request->post())) {
		    if( $model->save()){
            return $this->redirect(['view', 'id' => $model->bank_info_id]);
			}
			else{
			   Yii::$app->getSession()->setFlash('error', '当前银行卡已添加,请勿重复添加');
			    return $this->render('create', [
                'model' => $model,
				'bankname'=>$this->Get_Bank_name(),
				'menus' => $this->_getMenus(),
				'source' => $source,
            ]);
			}
        } else {
            return $this->render('create', [
                'model' => $model,
				'bankname'=>$this->Get_Bank_name(),
				'menus' => $this->_getMenus(),
				'source' => $source,
            ]);
        }
    }

    /**
     * Updates an existing Bankinfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->bank_info_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Bankinfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
 /*   public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
*/
    /**
     * Finds the Bankinfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bankinfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bankinfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
	*  开户行名称
	**/
	private function Get_Bank_name(){
		$bankname[] = '请选择银行名称';
		$bankname['中国银行'] = '中国银行';
		$bankname['中国工商银行'] = '中国工商银行';
		$bankname['中国农业银行'] = '中国农业银行';
		$bankname['中国建设银行'] = '中国建设银行';


		 return $bankname;
		}
	/**
	*  获取落地点Id
	**/
    private function GetTerminusUser()
    {
		 $value = '';
		 $TerminusUser = new TerminusUser();
         $info = $TerminusUser->getById(array('user_id'=>Yii::$app->user->id));
         if(!empty($info)){
		   $value = $info['terminus_id'];
		 }
		   return $value;
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
            'index' => ['menu' => '/bankinfo/index', 'item' => false],
            'create' => ['menu' => '/bankinfo/index', 'item' => false],
            'view' => ['menu' => '/bankinfo/index', 'item' => false],
        );

        return $arr[Yii::$app->controller->action->id];
    }
}
