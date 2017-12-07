<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnListAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/return';

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
