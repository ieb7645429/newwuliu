<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\AdminLoginForm;
use backend\models\AdminUser;
use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;
use yii\helpers\Url;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','check-pwd','reset-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
			        [
                        'actions' => ['reset-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
			        [
                        'actions' => ['check-pwd'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
			       'check-pwd'=>['post'],
			       'reset-password'=>['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $area = Yii::$app->user->identity->area;
        if ($area == 'sy') {
            return $this->redirect(Url::toRoute(['teller/index']));
        } else if($area == 'dl') {
            return $this->redirect(Url::toRoute(['/dl/teller/index']));
        }
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
	 /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token='111')
    {
        try {
			 $user = AdminUser::findOne([
            'status' => AdminUser::STATUS_ACTIVE,
			'id'=>Yii::$app->user->id,
            ]);
           if (!$user) {
               return false;
             }
		   if (!AdminUser::isPasswordResetTokenValid($user->password_reset_token)) {
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

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
	/**
	* ��֤������
	* С��
	**/
	public function actionCheckPwd(){
	  $pwd  = Yii::$app->request->post('pwd');
	  if(empty($pwd)){return false;}
	  $model= new PasswordResetRequestForm();
      $flag = $model->CheckPwd($pwd,Yii::$app->user->id);
	  return $flag;
	}
}
