<?php

namespace frontend\modules\hlj\controllers;

use Yii;
use frontend\modules\hlj\models\Area;
use common\models\AuthAssignment;
use frontend\models\CreateUserForm;
use yii\base\Exception;
use mdm\admin\components\MenuHelper;
use frontend\modules\hlj\models\User;
use frontend\modules\hlj\models\UserSearch;
use frontend\modules\hlj\models\BankInfo;
use common\yjmodels\Seller;
use common\yjmodels\Store;
use common\yjmodels\Member;
use common\models\UserAll;

class CreateUserController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->redirect(['search']);
    }
    
    public function actionSearch()
    {
        return $this->render('search',
            ['menus' => $this->_getMenus()]);
    }
    
    public function actionCreate() {
        $model = new CreateUserForm();
        $area = new Area();
        $auth = new AuthAssignment();
        if ($model->load(Yii::$app->request->post())) {
            $tr = Yii::$app->db_hlj->beginTransaction();
            try {
                $userInfo = User::findByUsername($model->username);
                if ($userInfo) {
                    if (empty($userInfo->small_num)) {
                        $userInfo->small_num = $model->small_num;
                        $userInfo->save();
                    }
                    
                } else {
                    if (!$user = $model->signup()) {
                        var_dump($model->errors);
                        throw new Exception('用户注册失败');
                    }
                    
                    if(!$auth -> saveMember($user->id)) {
                        var_dump($auth->errors);
                        throw new Exception('用户权限设置失败');
                    }
                }

                $tr -> commit();
                Yii::$app->getSession()->setFlash('success', '注册成功!<br>会员小号:'.$model->small_num);
                return $this->render('create', [
                    'model' => new CreateUserForm(),
                    'area' => $area,
                    'menus' => $this->_getMenus()
                ]);
            } catch (Exception $e) {
                $tr->rollBack();
                //                 echo $e->getMessage();die;
                //                 if($e->getMessage() == '用户权限设置失败') {
                Yii::$app->getSession()->setFlash('error', $e->getMessage());
                //                 }
            }
        }
            
        return $this->render('create', [
            'model' => $model,
            'area' => $area,
            'menus' => $this->_getMenus()
        ]);
    }
    
    public function actionUserList(){
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->editSearch(Yii::$app->request->queryParams);

        return $this->render('user-list',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus()
        ]);
    }
    
    public function actionUpdate($id)
    {
        try {
            $model = $this->findModel($id);
        }catch(NotFoundHttpException $e){
            Yii::$app->getSession()->setFlash('error', '用户不存在');
            return $this->redirect(['update','id'=>$id]);
        }
        $bankInfo = new BankInfo();
        $user = new User();
        $userAll = new UserAll();
        $bank = $bankInfo::findOne(['user_id'=>$id]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            try {
            $tr = Yii::$app->db->beginTransaction();
            $tr_hlj = Yii::$app->db_hlj->beginTransaction();
            $tr2 = Yii::$app->db2->beginTransaction();
                $user_post = Yii::$app->request->post('User');
                $userNeedEdit = 0;//判断会员信息是否需要修改
                $roles = array_keys(Yii::$app->authManager->getRolesByUser($model->id));
 
//                 if(!$this->isHLJMember($model->username)){//判断是否为黑龙江经销商
//                     Yii::$app->getSession()->setFlash('error', '账号必须为黑龙江经销商!');
//                     return $this->redirect(['update','id'=>$id]);
//                 }
                $module = \Yii::$app->getModule('hlj');
//                 if(!in_array($module->params['roleMember'], $roles) && (!$user->phoneFormat($user_post))){
//                     Yii::$app->getSession()->setFlash('error', '会员号必须为手机号!');
//                     return $this->redirect(['update','id'=>$id]);
//                 }
                
                if(in_array($module->params['roleMember'], $roles))
                {
                    if(!$user->phoneFormat($user_post))
                    {
                        Yii::$app->getSession()->setFlash('error', '会员号必须为手机号或座机号!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                }
                if($model->username!=$user_post['username']){//判断会员号是否需要修改
                    if(empty($user_post['username'])){
                        Yii::$app->getSession()->setFlash('error', '会员号不能为空!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    if(!$user->issetUserName($user_post['username'])){
                        Yii::$app->getSession()->setFlash('error', '会员号已存在!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    $model->username = $user_post['username'];
                    
                    //更新user_all表
                    $allModel = $userAll::findOne($id);
                    $allModel->username = $user_post['username'];
                    $all_re = $allModel->save();
                    
                    if(!$all_re){
                        Yii::$app->getSession()->setFlash('error', '用户账号修改失败!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    
                    $userNeedEdit = 1;
                }
                
                if($model->user_truename!=$user_post['user_truename']){//判断真实姓名是否需要修改
                    if(empty($user_post['user_truename'])){
                        Yii::$app->getSession()->setFlash('error', '真实姓名不能为空!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                    $model->user_truename = $user_post['user_truename'];
                    $userNeedEdit = 1;
                }
                
//                 修改友件网信息
//                 if($userNeedEdit==1){
//                     $youjian_re = $this->_youjianUserEdit($model);
//                     if(!$youjian_re){
//                         Yii::$app->getSession()->setFlash('error', '友件信息修改失败!');
//                         return $this->redirect(['update','id'=>$id]);
//                     }
//                 }
                $model->member_phone = $user_post['member_phone'];
                $wuliu_re = $model->save();
                
                
                if(!$wuliu_re){
                    Yii::$app->getSession()->setFlash('error', '会员信息修改失败!');
                    return $this->redirect(['update','id'=>$id]);
                }
            
            
                if(!empty(Yii::$app->request->post('BankInfo'))){
                    $post_bank = Yii::$app->request->post('BankInfo');
                    $bank->bank_info_card_no = $post_bank['bank_info_card_no'];
                    $bank->bank_info_account_name = $post_bank['bank_info_account_name'];
                    $bank->bank_info_bank_name = $post_bank['bank_info_bank_name'];
                    $bank->bank_info_bank_address = $post_bank['bank_info_bank_address'];
                    $bank_result = $bank->save();
                    if(!$bank_result){
                        Yii::$app->getSession()->setFlash('error', '银行卡信息修改失败!');
                        return $this->redirect(['update','id'=>$id]);
                    }
                }
            $tr->commit();
            $tr_hlj->commit();
            $tr2->commit();
            Yii::$app->getSession()->setFlash('success', '修改成功!');
            return $this->redirect(['update','id'=>$id]);
            }catch(Exception $e){
            $tr->rollBack();
            $tr_hlj->rollBack();
            $tr2->rollBack();
            Yii::$app->getSession()->setFlash('error', '修改失败');
            return $this->redirect(['update','id'=>$id]);
            }
        } else {
            return $this->render('update', [
                    'model' => $model,
                    'menus' => $this->_getMenus(),
                    'bank' => $bank,
            ]);
        }
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
            'search' => ['menu' => '/hlj/create-user/search', 'item' => false],
            'create' => ['menu' => '/hlj/create-user/create', 'item' => false],
            'user-list' => ['menu' => '/hlj/create-user/user-list', 'item' => false],
            'update' => ['menu' => '/hlj/create-user/user-list', 'item' => false],
        );
        
        return $arr[Yii::$app->controller->action->id];
    }
    private function _youjianUserEdit($model){
        $member = new Member();
        $seller = new Seller();
        $store = new Store();
        
        $re_store  = $store->storeInfoEdit($model);
        $re_seller = $seller->sellerInfoEdit($model);
        $re_member = $member->memberInfoEdit($model);
        if(!$re_store||!$re_seller||!$re_member){
            return false;
        }
        return true;
    }
    private function isHLJMember($username){
        $userAll = new UserAll();
        $model = $userAll::findOne(['username'=>$username]);
        if(empty($model)) return false;
        $auth = new AuthAssignment();
        if($auth::findOne(['user_id'=>$model->id])->item_name != '黑龙江用户') return false;
        return true;
    }

}
