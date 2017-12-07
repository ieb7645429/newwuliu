<?php

namespace frontend\controllers;

use Yii;
use common\models\BalanceLog;
use common\models\BalanceLogSearch;
use common\models\WithdrawalLogSearch;
use common\models\UserBalance;
use common\models\UserBalanceSearch;
use common\models\LogisticsOrderFushunSearch;
use common\models\LogisticsOrderFushun;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BalanceLogController implements the CRUD actions for BalanceLog model.
 */
class BalanceLogController extends Controller
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
     * Lists all BalanceLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BalanceLogSearch();
        $dataProvider = $searchModel->search2(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionAble()
    {
        $searchModel = new WithdrawalLogSearch();
        $dataProvider = $searchModel->search2(Yii::$app->request->queryParams);
        
        return $this->render('able', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all UserBalance models.
     * @return mixed
     */
    public function actionBalance()
    {
        $searchModel = new UserBalanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('balance', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionWithDrawal(){
        $model = new UserBalance();
        $tr = Yii::$app->db->beginTransaction();
        try{
            $res = $model->editwithdrawal(Yii::$app->request->post('amount'),Yii::$app->request->post('userId'));
            if($res === false){
                throw new Exception('提现失败', '1');
            }
            $result = ['error'=>0,'message'=>'提现成功'];
            $tr -> commit();
        }catch(Exception $e){
            $tr->rollBack();
            $result = ['error'=>1,'data'=>'提现失败'];
        }
        return json_encode($result);
    }
    
    /**
     * Lists all LogisticsOrder models.
     * @return mixed
     */
    public function actionFushun()
    {
        $searchModel = new LogisticsOrderFushunSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $goods_num = empty($dataProvider->query->sum('goods_num'))?0:$dataProvider->query->sum('goods_num');
        $type = '';
        return $this->render('fushun', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'goods_num' => $goods_num,
            'indexOver'=>$type
        ]);
    }
    
    /**
     * Displays a single LogisticsOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        return $this->render('fushunview', [
            'model' => LogisticsOrderFushun::findOne($id),
            'role' => $role,
        ]);
    }

    /**
     * Finds the BalanceLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return BalanceLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BalanceLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
