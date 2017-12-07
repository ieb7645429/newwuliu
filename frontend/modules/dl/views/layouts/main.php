<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
// use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;
// use mdm\admin\components\Helper;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<div class="print-div"></div>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '物流系统',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
        'innerContainerOptions' => [
            'class' => 'container-fluid',
        ]
    ]);
    
//     if (Yii::$app->user->isGuest) {
//         $menuItems = [
//             ['label' => '同城端', 'url' => ['/employee/index']],
//             ['label' => '司机端', 'url' => ['/driver/index']],
//             ['label' => '落地点端', 'url' => ['/terminus/index']],
//             ['label' => '用户端', 'url' => ['/member/index']],
//         ];
//     } else {
//         $menuItems = array();
//         if(Helper::checkRoute('/employee/index')) {
//             $menuItems[] = ['label' => '同城端', 'url' => ['/employee/index']];
//         }
//         if(Helper::checkRoute('/instock/index')) {
//             $menuItems[] = ['label' => '同城端', 'url' => ['/instock/index']];
//         }
//         if(Helper::checkRoute('/return-complete/index')) {
//             $menuItems[] = ['label' => '同城端', 'url' => ['/return-complete/index']];
//         }
//         if(Helper::checkRoute('/driver/index')) {
//             $menuItems[] = ['label' => '司机端', 'url' => ['/driver/index']];
//         }
//         if(Helper::checkRoute('/terminus/index')) {
//             $menuItems[] = ['label' => '落地点端', 'url' => ['/terminus/index']];
//         }
//         if(Helper::checkRoute('/member/index')) {
//             $menuItems[] = ['label' => '用户端', 'url' => ['/member/index']];
//         }
//     }
    $menuItems = array();
    if (Yii::$app->user->isGuest) {
//         $menuItems[] = ['label' => '注册', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
		$menuItems[] = '<li>'
            . Html::beginForm(['/site/reset-password'], 'post')
            . Html::submitButton(
                '修改密码',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '退出 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';		
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container-fluid">
        <?= Alert::widget() ?>
        <div class="row">
          <div class="col-md-2">
          </div>
          <div class="col-md-10">
            <?php if(isset($this->params['leftmenus']['items'])):?>
            <ul class="nav nav-pills">
              <?php foreach ($this->params['leftmenus']['items'] as $item):?>
              <li role="presentation" class="<?=isset($item['active'])?$item['active']:''?>"><a href="<?= Url::toRoute($item['url']) ?>"><?=$item['label'] ?></a></li>
              <?php endforeach;?>
            </ul>
            <?php endif;?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2">
            <div class="list-group">
            <?php if(isset($this->params['leftmenus']['menus'])):?>
              <?php foreach ($this->params['leftmenus']['menus'] as $menu):?>
              <a href="<?= Url::toRoute($menu['url']) ?>" class="list-group-item <?=isset($menu['active'])?$menu['active']:''?>"><?= $menu['label']?></a>
              <?php endforeach;?>
            <?php endif;?>
            </div>
          </div>
          <div class="col-md-10">
            <?php /*Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]); */?>
            <?= $content ?>
          </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; 物流管理平台 <?= date('Y') ?></p>

        <p class="pull-right"></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
