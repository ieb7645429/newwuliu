<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CreateUserCreateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/create-user';

    public $js = [
        'create.js'
    ];
    
    public $css = [
        'create.css'
    ];
}
