<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SiteAboutAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/site';

    public $css = [
        'about.css',
    ];
    
    public $js = [
        'about.js'
    ];
}
