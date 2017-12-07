<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnCompleteAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/return-complete';

    public $css = [
            'index.css'
    ];
    public $js = [
        'index.js'
    ];
    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}
