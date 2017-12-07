<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class TerminusAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/terminus';

    public $css = [
        'index.css',
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
