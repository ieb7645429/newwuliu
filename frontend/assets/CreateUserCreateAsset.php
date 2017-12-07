<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CreateUserCreateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/create-user';

    public $js = [
        'create.js'
    ];
    
    public $css = [
        'create.css'
    ];
}
