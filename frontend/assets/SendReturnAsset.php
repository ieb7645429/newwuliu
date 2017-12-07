<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SendReturnAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/send';

    public $css = [
        'list.css',
    ];
    
    public $js = [
        'list.js'
    ];
    
    public $depends = [
        'components\Lodop\LodopAsset'
    ];
}
