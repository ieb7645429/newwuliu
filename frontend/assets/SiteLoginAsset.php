<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SiteLoginAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/site';

    public $css = [
        'login.css',
    ];
}
