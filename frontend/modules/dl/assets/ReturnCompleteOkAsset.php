<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnCompleteOkAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/return-complete';

    public $css = [
            'return-ok.css'
    ];
    public $js = [
        'return-ok.js'
    ];
   
}
