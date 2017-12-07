<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnedAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/return';

    public $css = [
        'list.css',
    ];
    
    public $js = [
        'returned.js'
    ];
    
    public $depends = [
        'components\Lodop\DalianAsset'
    ];
}
