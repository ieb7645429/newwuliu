<?php
namespace frontend\modules\dl\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\modules\dl\models\LoginForm;
use frontend\modules\dl\models\User;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\modules\dl\models\SignupForm;
use frontend\modules\dl\models\ContactForm;
use frontend\modules\dl\models\Area;
use frontend\modules\dl\models\AuthAssignment;
use yii\db\Exception;
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
                'only' => ['logout', 'signup','check-pwd','reset-password'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->_redirect();
        //return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = User::findIdentity(Yii::$app->user->id);
            if($user->member_provinceid == 6) {
                if ($user->member_cityid == 107) {
                    $this->_redirect();
                } else if($user->member_cityid == 108) {
                    $this->_dlRedirect();
                }
            } else if($user->member_provinceid == 8) {
                $this->_hljRedirect();
            }
        } else {
            $this->layout = 'login';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    
    private function _redirect() {
        $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        if (in_array(Yii::$app->params['roleMember'], $roles)) {
            return $this->redirect(Url::to(['member/index']));
        } else if (in_array(Yii::$app->params['roleEmployee'], $roles)) {
            return $this->redirect(Url::to(['employee/index']));
        } else if (in_array('瑞胜开单员', $roles)) {
            return $this->redirect(Url::to(['employee/index']));
        } else if (in_array(Yii::$app->params['roleDriver'], $roles)) {
            return $this->redirect(Url::to(['driver/index']));
        } else if (in_array(Yii::$app->params['roleTerminus'], $roles)) {
            return $this->redirect(Url::to(['terminus/index']));
        } else if (in_array(Yii::$app->params['PutInStorage'], $roles)) {
            return $this->redirect(Url::to(['instock/index']));
        } else if (in_array(Yii::$app->params['roleReturn'], $roles)) {
            return $this->redirect(Url::to(['return-complete/index']));
        } else if (in_array(Yii::$app->params['roleDriverManager'], $roles)) {
            return $this->redirect(Url::to(['driver-manager/index']));
        } else if (in_array(Yii::$app->params['roleAllReturn'], $roles)) {
            return $this->redirect(Url::to(['return/index']));
        } else if (in_array(Yii::$app->params['roleUserManager'], $roles)) {
            return $this->redirect(Url::to(['create-user/index']));
        } else if (in_array(Yii::$app->params['roleDriverManagerCityWide'], $roles)) {
            return $this->redirect(Url::to(['driver-manager/index']));
        } else if (in_array(Yii::$app->params['roleEmployeeDelete'], $roles)) {
            return $this->redirect(Url::to(['employee/index']));
        } else if (in_array('管理员', $roles)) {
            return $this->redirect(Url::to(['employee/index']));
        } else if (in_array('密码修改', $roles)) {
            return $this->redirect(Url::to(['user/reset-pwd']));
        } else if (in_array('查询线路',$roles)){
            return $this->redirect(Url::to(['customer/index']));
        } elseif (in_array('用户管理密码修改',$roles)){
            return $this->redirect(Url::to(['user/reset-pwd']));
        }else {
            return $this->goBack();
        }
    }
    private function _hljRedirect() {
        $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        $module = \Yii::$app->getModule('hlj');
        if(in_array($module->params['roleEmployee'], $roles)) {
            return $this->redirect(Url::to(['hlj/employee/index']));
        }
    }
    
    private function _dlRedirect() {
        $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        $module = \Yii::$app->getModule('dl');
        if(in_array($module->params['roleEmployee'], $roles)) {
            return $this->redirect(Url::to(['dl/employee/index']));
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        $area = new Area();
        $auth = new AuthAssignment();
        if ($model->load(Yii::$app->request->post())) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                if (!$user = $model->signup()) {
                    throw new Exception('用户注册失败', $model->errors);
                }

                if(!$auth -> saveMember($user->id)) {
                    throw new Exception('用户权限设置失败', $auth->errors);
                }
                $tr -> commit();
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            } catch (Exception $e) {
                $tr->rollBack();
//                 echo $e->getMessage();die;
//                 if($e->getMessage() == '用户权限设置失败') {
                    Yii::$app->getSession()->setFlash('error', $e->getMessage());
//                 }
            }
        }

        return $this->render('signup', [
            'model' => $model,
            'area' => $area
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }
       return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
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
			 $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
			'id'=>Yii::$app->user->id,
            ]);
           if (!$user) {
               return false;
             }
		   if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
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
	* 验证旧密码
	* 小雨
	**/
	public function actionCheckPwd(){
	  $pwd  = Yii::$app->request->post('pwd');
	  if(empty($pwd)){return false;}
	  $model= new PasswordResetRequestForm();
      $flag = $model->CheckPwd($pwd,Yii::$app->user->id);
	  return $flag;
	}
}
