<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class TerminusPayAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/terminus';

    public $js = [
        'pay.js'
    ];
}
