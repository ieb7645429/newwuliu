<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SendReturnedAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/send';

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
