<?php

namespace frontend\modules\dl\controllers;

use Yii;
use frontend\modules\dl\models\User;
use frontend\modules\dl\models\UserSearch;
use mdm\admin\components\MenuHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use common\models\UserAll;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
			       'check-uid'=>['POST'],
			     //  'reset-pwd'=>['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
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
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
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
                'index' => ['menu' => '/dl/user/index', 'item' => false],
                'reset-pwd' => ['menu' => '/dl/user/reset-pwd', 'item' => false],
                'user-list' => ['menu' => '/dl/create-user/user-list', 'item' => false],
        );
    
        return $arr[Yii::$app->controller->action->id];
    }
	/**
	*  ͨ���ֻ����޸�ָ������
	*  2017-09-08
	*  xiaoyu
	**/
   public function actionResetPwd()
    {
        try {
             if(Yii::$app->request->post('phone')){
				  $user = UserAll::findOne([
		          'status' => UserAll::STATUS_ACTIVE,
				  'username'=>Yii::$app->request->post('phone'),
				 ]);
			 }
			 else{
			     $user = UserAll::findOne([
			     'status' => UserAll::STATUS_ACTIVE,
				'id'=>Yii::$app->user->id,
				]);
			 }
			
           if (!$user) {
               return false;
             }
             if (!UserAll::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
              }
            }
			$token = $user->password_reset_token;
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
       //     return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
            'menus' => $this->_getMenus(),
        ]);
		
    }
	/**
	* ��֤�ֻ������Ƿ����,������������
	*  2017-09-08
	*  xiaoyu
	**/
    public function actionCheckUid(){
	  $pwd  = Yii::$app->request->post('phone');
	  if(empty($pwd)){return false;}
	  $user = UserAll::findOne([
            'status' => UserAll::STATUS_ACTIVE,
			'username'=>Yii::$app->request->post('phone'),
            'area'=>'dl',
            ]);
      if (!$user){
         return false;
       }
	  else{
	     return true;
	  }
	}
}
