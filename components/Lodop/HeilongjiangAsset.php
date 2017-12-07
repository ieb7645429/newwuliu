<?php

namespace components\Lodop;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class HeilongjiangAsset extends AssetBundle
{
    public $sourcePath = '@components/Lodop';
    
    public $js = [
        'lib/LodopFuncs.js',
        'heilongjiang.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
