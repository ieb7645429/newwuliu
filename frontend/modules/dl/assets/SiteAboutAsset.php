<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SiteAboutAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/site';

    public $css = [
        'about.css',
    ];
    
    public $js = [
        'about.js'
    ];
}
