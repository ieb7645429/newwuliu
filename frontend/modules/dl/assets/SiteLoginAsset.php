<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SiteLoginAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/site';

    public $css = [
        'login.css',
    ];
}
