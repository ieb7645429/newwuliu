<?php

namespace frontend\controllers;

use common\models\AuthItem;
use common\models\DriverRoute;
use Yii;

use common\models\LogisticsCar;
use common\models\LogisticsRoute;
use common\models\Driver;
use common\models\AuthAssignment;
use common\models\User;
use common\models\RouteSearch;
use common\models\Area;
use common\models\LogisticsArea;
use frontend\models\CreateUserForm;
use frontend\controllers\CreateUserController;
use mdm\admin\components\MenuHelper;
use frontend\models\SignupForm;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\rbac\Assignment;
use yii\web\Response;

/**
 * Class AdditionController
 */
class AdditionController extends \yii\web\Controller
{

    /**
     * 添加司机
     * @Author:Fenghuan
     * @Date:2017/11/7 Tuesday
     */
    public function actionDriver()
    {
        $model_auth_assignment = new authAssignment();
        $id = Yii::$app->request->get('id');
        if (!$id) {
            Yii::$app->getSession()->setFlash('error', '操作失败');
        }
        $modelLogisticsRoute = new LogisticsRoute();
        $modelCar = new LogisticsCar();


        //当前线路下的所有member_id
        $condition = $modelCar->getUsersByCondition(['logistics_car.logistics_route_id' => $id], ['driver.member_id']);
        $condition = ArrayHelper::getColumn($condition, 'member_id');


        //查出所有非当前线路的司机
        $resNotExist = $model_auth_assignment->selectItems(['and', ['not in', 'user_id', $condition], ['item_name' => '司机']], ['auth_assignment.user_id', 'user_all.username']);
        $resExist = $model_auth_assignment->selectItems(['and', ['in', 'user_id', $condition], ['item_name' => '司机']], ['auth_assignment.user_id', 'user_all.username']);

        $infoExist = ArrayHelper::map($resExist, 'user_id', 'username');
        $infoNotExist = ArrayHelper::map($resNotExist, 'user_id', 'username');

        $model = $modelLogisticsRoute->getLogisticsRouteFindOne($id);

        return $this->render('driverss', [
            'model' => $model,
            'id' => $id,
            'infoExist' => $infoExist,
            'infoNotExist' => $infoNotExist,
            'menus' => $this->_getMenus()
        ]);

    }


    /**
     * 线路修改
     * Updates an existing LogisticsRoute model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @internal param int $id
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');

        $model_logistics_route = new LogisticsRoute();
        $model_user = new User();
        $model_logistics_area = new LogisticsArea();
        $model_logistics_car = new LogisticsCar(['scenario' => 'car_number']);
        $model_area = new Area();
        $model_auth_item = new AuthItem();
        $model_auth_assignment = new AuthAssignment();
        $model_driver = new Driver();


        //01 当前线路名
        $model = $model_logistics_route->getLogisticsRouteFindOne($id);


        //线路对应的所有车
//        $carInfo = $model_logistics_car->selectCars(['logistics_route_id' => $id]);



        //线路对应的所有司机
        $driversRes = $model_logistics_car->getDriversByRoute(['logistics_route_id' => $id], ['user.username', 'user.id']);
        $driversRes = ArrayHelper::map($driversRes, 'id', 'username');

        //联动信息
        $logiscticAreaInfo = $model_logistics_area->findOneModel(
            ['province_id AS member_provinceid', 'city_id AS member_cityid', 'area_id AS member_areaid'],
            ['logistics_area.logistics_route_id' => $id]
        );

//        $roles = $model_auth_item->getRolesList(['type' => '1']);

        //查出省或者市
        $districtId = $this->_getDistrict($id, $model_logistics_area);
        if ($districtId) {
            $model_area = Area::findModel($districtId);
        }

        return $this->render('update', [
            'model' => $model,
            'modelCar' => $model_logistics_car,
//            'carInfo' => $carInfo,
            'driversRes' => $driversRes,
            'authTiem' => $model_auth_item,
            'area' => $model_area,
            'logiscticAreaInfo' => $logiscticAreaInfo,
            'model_driver' => $model_driver,
            'id' => $id,
            'menus' => $this->_getMenus(),
        ]);


    }

    /**
     * 修改/删除
     * @Author:Fenghuan
     */
    public function actionUpdateRoute()
    {
        $request = Yii::$app->request->post();
        $model_logistics_route = new LogisticsRoute();
        $model_logistics_area = new LogisticsArea();
        $model_logistics_car = new LogisticsCar();
        $model_driver = new Driver();
        $model_area = new Area();

        if ($model_logistics_route->load($request)
            && $model_logistics_area->load($request)
            && $model_logistics_car->load($request)
            && $model_area->load($request)
            && $model_driver->load($request)
        ) {
            //modification(修改, 根据已有线路可以增加, 可以在相同的界面)
            if ($request['subType'] === 'modification') {

                try {
                    $transaction = Yii::$app->db->beginTransaction();

                    $msg = '修改成功';

                    if (!$model_logistics_route->logistics_route_id) {
                        throw new Exception('未选中线路', '186');
                    }

                    //修改线路
                    $model_logistics_route->updateRoute(
                        [
                            'logistics_route_name' => $model_logistics_route->logistics_route_name,
                            'logistics_route_code' => $model_logistics_route->logistics_route_code,
                            'logistics_route_no' => $model_logistics_route->logistics_route_no,
                            'same_city' => $model_logistics_route->same_city,
                        ],
                        ['logistics_route_id' => $model_logistics_route->logistics_route_id]
                    );


                    //update logistics_area
                    LogisticsArea::updateLogisticsArea(
                        [
                            'province_id' => $model_logistics_area->member_provinceid ? $model_logistics_area->member_provinceid : 0,
                            'city_id' => $model_logistics_area->member_cityid ? $model_logistics_area->member_cityid : 0,
                            'area_id' => $model_logistics_area->member_areaid ? $model_logistics_area->member_areaid : 0,
                        ],
                        ['logistics_route_id' => $model_logistics_route->logistics_route_id]
                    );

                    //同时填写新车牌号,和老车牌号, 才能修改老车牌号
//                    if ($model_logistics_car->logistics_car_id && $model_logistics_car->car_number) {
                    if (!empty($model_logistics_car->car_number[1])) {

                        $carId = $model_driver->findOneModel(['member_id' => $model_driver->driver_id]);
                        if(!empty($carId)){
                            //修改车
//                            var_dump($model_logistics_car->car_number[1],$carId->logisticsCarInfo->logistics_car_id,$model_logistics_route->logistics_route_id);die;
                            $model_logistics_car->updateCar(
//                            ['car_type_id' => (int)$model_logistics_route->same_city, 'car_number' => $model_logistics_car->car_number],
                                ['car_number' => $model_logistics_car->car_number[1]],
                                ['logistics_car_id' => $carId->logisticsCarInfo->logistics_car_id, 'logistics_route_id' => $model_logistics_route->logistics_route_id]
                            );
                        }

                    }

                    //线路对应所有司机car_type_id 都变
                    $model_logistics_car->updateCar(['car_type_id' => $model_logistics_route->same_city], ['logistics_route_id' => $model_logistics_route->logistics_route_id]);

                    //update pinyin
                    if ($model_logistics_area->member_areaid) {
                        $model_area->updateArea(['pinyin_name' => $model_area->pinyin_name], ['area_id' => $model_logistics_area->member_areaid]);
                    } else if ($model_logistics_area->member_cityid) {
                        $model_area->updateArea(['pinyin_name' => $model_area->pinyin_name], ['area_id' => $model_logistics_area->member_cityid]);
                    }

                    $transaction->commit();

                    Yii::$app->getSession()->setFlash('success', $msg);

                    return $this->redirect(['update', 'id' => $model_logistics_route->logistics_route_id]);


                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', $e->getMessage());
                }

            }

            else if ($request['subType'] === 'delCarDriver') {

                $carId = $model_driver->findOneModel(['member_id' => $model_driver->driver_id]);

                if(!empty($carId)){
                    //同时删除车和司机(取消car表跟线路的关联)
                    $updateRes1 = $model_logistics_car->updateCar(
                        ['logistics_route_id' => 0],
                        ['logistics_car_id' => $carId->logisticsCarInfo->logistics_car_id]
                    );
                }

                Yii::$app->getSession()->setFlash('success', '删除成功');
                return $this->redirect(['update', 'id' => $model_logistics_route->logistics_route_id]);
            }

            Yii::$app->getSession()->setFlash('success', '操作成功');
            return $this->redirect(['update', 'id' => $model_logistics_route->logistics_route_id]);

        } else {
            Yii::$app->getSession()->setFlash('success', '数据异常');
            return $this->goBack();
        }
    }



    /**
     * 先通过区找拼音, 找不到再通过市
     * @Author:Fenghuan
     * @param $id
     * @param $model_logistics_area
     * @return string
     */
    private function _getDistrict($id, $model_logistics_area)
    {
        $item = $model_logistics_area->findOneModel('*', ['logistics_route_id' => $id]);

        if (!$item) return '';

        if ($item->area_id) {
            return $item->area_id;
        } else if ($item->city_id) {
            return $item->city_id;
        } else {
            return '';
        }

    }


    /**
     * 根据所选司机获取车牌号
     * @Author:Fenghuan
     */
    public function actionGetCarNumber()
    {
        $model = new Driver();

        $driverId = Yii::$app->request->post('id');

        $carNumber = $model->findOneModel(['member_id' => $driverId]);

        if(!empty($carNumber)){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['code' => 200, 'data' => $carNumber->logisticsCarInfo->car_number];
        }

    }


    /**
     * 检测拼音是否存在
     * @Author:Fenghuan
     */
    public function actionCheckPinYin()
    {
        $request = Yii::$app->request;
        $district = $request->post('pinyin_name');
        $model = new Area();

        $res = $model->getAreaInfo(['area_id' => $district]);//pinyin_name is right or not

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['code' => 200, 'data' => ['pinyin_name' => isset($res->pinyin_name) ? $res->pinyin_name : '']];

    }


    /**
     * 显示页面(版本一)
     * @Author:Fenghuan
     */
    public function actionIndex()
    {
        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus(),
        ]);
    }


    /**
     * 添加线路相关信息(版本一, 添加完跳转到add)
     * @Author:Fenghuan
     */
    public function actionAddRouteOne()
    {
        $request = Yii::$app->request->post();
        $area = new Area();
        $model = new LogisticsRoute();
        $modelLogisticsArea = new LogisticsArea();
        if ($model->load($request) && $modelLogisticsArea->load($request)) {

            try {
                $transaction = Yii::$app->db->beginTransaction();

                //insert route
                if (!$model->save()) {
                    throw new Exception('保存失败1');
                }

                //insert logistics_area
                $modelLogisticsArea->logistics_route_id = $model->logistics_route_id;
                $modelLogisticsArea->province_id = $modelLogisticsArea->province_id ? $modelLogisticsArea->province_id : 0;
                $modelLogisticsArea->city_id = $modelLogisticsArea->city_id ? $modelLogisticsArea->city_id : 0;
                $modelLogisticsArea->area_id = $modelLogisticsArea->area_id ? $modelLogisticsArea->area_id : 0;
                if (!$modelLogisticsArea->save()) {
                    throw new Exception('保存失败2');
                }

                if ($area->pinyin_name) {
                    if ($modelLogisticsArea->area_id) {
                        //插入区拼音
                        $area->updateArea(['pinyin_name' => $area->pinyin_name], ['area_id' => $modelLogisticsArea->area_id]);

                    } else if ($modelLogisticsArea->city_id) {
                        //插入市拼音
                        $area->updateArea(['pinyin_name' => $area->pinyin_name], ['area_id' => $modelLogisticsArea->city_id]);
                    }
                }

                $transaction->commit();

                Yii::$app->getSession()->setFlash('success', '操作成功');

                return $this->redirect([
                    'car-driver',
                    'logistics_route_id' => $model->logistics_route_id,
                ]);


            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', $e->getMessage());

            }

        }

        return $this->render('addone', [
            'model' => $model,
            'modelLogisticsArea' => $modelLogisticsArea,
            'area' => $area,
            'menus' => $this->_getMenus(),

        ]);

    }

    /**
     * @Author:Fenghuan
     */
    public function actionCarDriver()
    {
        $modelRoute = new LogisticsRoute();
        $modelDriver = new Driver();
        $area = new Area();
        $modelLogisticsCar = new LogisticsCar();
        $modelAssignment = new AuthAssignment();
        $modelCar = new LogisticsCar();


        //注册用户用, 添加司机场景
        $modelCreate = new CreateUserForm(['scenario' => 'driver']);

        $request = Yii::$app->request;
        $logistics_route_id = $request->get('logistics_route_id');


        //load
        if ($modelCreate->load($request->post()) && $modelCar->load($request->post())) {

            try {
                $tr = Yii::$app->db->beginTransaction();

                if(!$modelCar->logistics_route_id && !$logistics_route_id){
                    throw new Exception('没有这个线路');
                }

                //insert car
                if (!$modelCar->save()) {
                    throw new Exception('操作失败');
                }

                //insert User table
                $userInfo = $modelCreate->signup();

                if (!$userInfo) {
                    throw new Exception('信息是否完整或用户名已重复 ?');
                }

                //insert Driver table
                $modelDriver->member_id = $userInfo->id;//user_id
                $modelDriver->add_time = (string)$_SERVER['REQUEST_TIME'];
                $modelDriver->logistics_car_id = $modelCar->logistics_car_id;
                if (!$modelDriver->save()) {
                    throw new Exception('保存失败2');
                }

                //insert SignupForm 没有判断user_id的重复性, 因为可能有多个角色
                $modelAssignment->item_name = '司机';//name of role
                $modelAssignment->user_id = (string)$userInfo->id;
                $modelAssignment->created_at = $_SERVER['REQUEST_TIME'];
                if (!$modelAssignment->save()) {
                    throw new Exception('保存失败3');
                }

                $tr->commit();

                $res = $modelRoute->getLogisticsRouteFindOne($modelCar->logistics_route_id);
                $modelRoute->logistics_route_name = $res->logistics_route_name;
                $modelCar->logistics_route_id = isset($res->logistics_route_name) ? $res->logistics_route_name : '';
                $modelCar->car_type_id = isset($res->same_city) ? $res->same_city : 0;


                Yii::$app->getSession()->setFlash('success', '添加成功');

                return $this->render('card', [
                    'modelCar' => $modelCar,
                    'logistics_route_id' => isset($logistics_route_id) ? $logistics_route_id : '',
                    'modelDriver' => $modelDriver,
                    'car_number' => isset($res->car_number) ? $res->car_number : '',
                    'logistics_car_id' => isset($logistics_car_id) ?: '',
                    'area' => $area,
                    'signupForm' => $modelCreate,
                    'modelRoute' => $modelRoute,
                    'menus' => $this->_getMenus(),
                ]);

            } catch (Exception $e) {
                $tr->rollBack();

                Yii::$app->getSession()->setFlash('error', $e->getMessage());

            }

        }

        if (!empty($logistics_route_id)) {

            $res = $modelRoute->getLogisticsRouteFindOne($logistics_route_id);
            $modelRoute->logistics_route_name = $res->logistics_route_name;
            $modelCar->logistics_route_id = isset($res->logistics_route_name) ? $res->logistics_route_name : '';
            $modelCar->car_type_id = isset($res->same_city) ? $res->same_city : 0;
        }

        return $this->render('card', [
            'modelCar' => $modelCar,
            'logistics_route_id' => isset($logistics_route_id) ? $logistics_route_id : '',
            'modelDriver' => $modelDriver,
            'logistics_car_id' => isset($logistics_car_id) ?: '',
            'area' => $area,
            'signupForm' => $modelCreate,
            'modelRoute' => $modelRoute,
            'menus' => $this->_getMenus(),
        ]);

    }


    /**
     * 根据输入线路名返回信息(版本一)
     * @Author:Fenghuan
     * @return array
     */
    public function actionGetRoute()
    {
        $model = new LogisticsRoute();
        $request = Yii::$app->request;
        $logistics_route_name = $request->post('logistics_route_name');

        $res = $model->getLogisticsRouteInfo(['logistics_route_name' => $logistics_route_name]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['code' => 200, 'data' => $res[0]];

    }


    /**
     * 添加司机
     * @Author:Fenghuan
     * @Date:2017/11/7
     */
    public function actionAddDrivers()
    {
        $request = Yii::$app->request;
        $nameArr = $request->post('username');
        $logistics_route_id = $request->post('logistics_route_id');
        $modelDriver = new Driver();
        $modelCar = new LogisticsCar();

        if (!$logistics_route_id || empty($nameArr)) {
            Yii::$app->getSession()->setFlash('error', '数据有误');
            return $this->redirect(['driver', 'id' => $logistics_route_id]);
        }

        //当前线路增加司机,把该用户对应的logistics_car.logistics_route_id修改成当前线路id
        if ($request->post('stype') == 'add') {
            foreach($nameArr as $v){
                $data = $modelDriver->findOneModel(['member_id' => $v]);
                if(!empty($data)){
                    $logisticsCarId = $data->logistics_car_id;
                    $updateRes1 = $modelCar->updateCar(
                        ['logistics_route_id' => $logistics_route_id],
                        ['logistics_car_id' => $logisticsCarId]
                    );
                    if(!$updateRes1){
                        Yii::$app->getSession()->setFlash('error', '添加失败');
                        return $this->redirect(['driver', 'id' => $logistics_route_id]);
                    }
                }

            }

        }
        //当前线路删除一个司机, update logistics_car set logistics_route_id = 0 ;
        else if ($request->post('stype') == 'remove') {
            foreach ($nameArr as $v) {
                $data = $modelDriver->findOneModel(['member_id' => $v]);
                if(!empty($data)){
                    $logisticsCarId = $data->logistics_car_id;
                    $updateRes1 = $modelCar->updateCar(
                        ['logistics_route_id' => 0],
                        ['logistics_car_id' => $logisticsCarId]
                    );
                    if(!$updateRes1){
                        Yii::$app->getSession()->setFlash('error', '删除失败');
                        return $this->redirect(['driver', 'id' => $logistics_route_id]);
                    }
                }
            }

        } else {
            Yii::$app->getSession()->setFlash('error', '操作有误');
            return $this->redirect(['driver', 'id' => $logistics_route_id]);
        }

        return $this->redirect(['driver', 'id' => $logistics_route_id]);

    }



    /**
     * 取得menus
     * @Author:Fenghuan
     * @return array
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

    /**
     * @Author:Fenghuan
     * @return mixed
     */
    private function _getActiveMenu() {
        $arr = array(

            'index' => ['menu' => '/addition/index', 'item' => false],
            'update' => ['menu' => '/addition/update', 'item' => false],
            'add-route-one' => ['menu' => '/addition/add-route-one', 'item' => false],
            'car-driver' => ['menu' => '/addition/car-driver', 'item' => false],
            'driver' => ['menu' => '/addition/driver', 'item' => false],

        );

        return $arr[Yii::$app->controller->action->id];
    }

    /**
     * 添加线路相关信息(版本二)
     * @Author:Fenghuan
     */
//    public function actionAddRoute()
//    {
//        $request = Yii::$app->request->post();
//        $area = new Area();
//        $model = new LogisticsRoute();
//        $modelLogisticsArea = new LogisticsArea();
//        if ($model->load($request) && $modelLogisticsArea->load($request)) {
//
//            try {
//                $transaction = Yii::$app->db->beginTransaction();
//
//                //insert route
//                if (!$model->save()) {
//                    throw new Exception('保存失败1');
//                }
//
//                //insert logistics_area
//                $modelLogisticsArea->logistics_route_id = $model->logistics_route_id;
//                $modelLogisticsArea->province_id = $modelLogisticsArea->province_id ? $modelLogisticsArea->province_id : 0;
//                $modelLogisticsArea->city_id = $modelLogisticsArea->city_id ? $modelLogisticsArea->city_id : 0;
//                $modelLogisticsArea->area_id = $modelLogisticsArea->area_id ? $modelLogisticsArea->area_id : 0;
//                if (!$modelLogisticsArea->save()) {
//                    throw new Exception('保存失败2');
//                }
//
//                if ($area->pinyin_name) {
//                    if ($modelLogisticsArea->area_id) {
//                        //插入区拼音
//                        $area->updateArea(['pinyin_name' => $area->pinyin_name], ['area_id' => $modelLogisticsArea->area_id]);
//
//                    } else if ($modelLogisticsArea->city_id) {
//                        //插入市拼音
//                        $area->updateArea(['pinyin_name' => $area->pinyin_name], ['area_id' => $modelLogisticsArea->city_id]);
//                    }
//                }
//
//                $transaction->commit();
//
//                Yii::$app->getSession()->setFlash('success', '操作成功');
//
//                return $this->render('add', [
//                    'model' => $model,
//                    'modelLogisticsArea' => $modelLogisticsArea,
//                    'area' => $area,
//                    'menus' => $this->_getMenus(),
//
//                ]);
//
//            } catch (Exception $e) {
//                $transaction->rollBack();
//                Yii::$app->getSession()->setFlash('error', $e->getMessage());
//
//            }
//
//        }
//
//        return $this->render('add', [
//            'model' => $model,
//            'modelLogisticsArea' => $modelLogisticsArea,
//            'area' => $area,
//            'menus' => $this->_getMenus(),
//
//        ]);
//
//    }


    /**
     * 显示页面(版本一和二)
     * @Author:Fenghuan
     * @return string
     */
//    public function actionModification()
//    {
//        $searchModel = new RouteSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('modification', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//            'menus' => $this->_getMenus(),
//        ]);
//    }


    /**
     * get the driver with the car(现在不用了)
     * @Author:Fenghuan
     */
//    public function actionGetDriver()
//    {
//        $model = new Driver();
//        $request = Yii::$app->request;
//        $logistics_car_id = $request->post('driver_id');
//
//        $modelUser = $model->findOneModel(['logistics_car_id' => $logistics_car_id]);
//
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        return ['code' => 200, 'data' => $modelUser->driverJoinUser->username];
//    }



}

