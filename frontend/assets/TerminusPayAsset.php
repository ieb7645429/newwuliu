<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class TerminusPayAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/terminus';

    public $js = [
        'pay.js'
    ];
}
