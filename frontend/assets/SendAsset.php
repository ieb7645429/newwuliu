<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SendAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/send';

    public $css = [
            'index.css',
    ];

    public $js = [
            'index.js'
    ];

    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}