<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverReturnAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/driver';
    
    public $css = [
        'list.css',
    ];
    
    public $js = [
        'list.js'
    ];
    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}
