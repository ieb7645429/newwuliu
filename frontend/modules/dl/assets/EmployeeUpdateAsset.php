<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EmployeeUpdateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/employee';
    
    public $css = [
            'create.css',
    ];
    public $js = [
        'update.js'
    ];
}
