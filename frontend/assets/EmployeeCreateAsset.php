<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EmployeeCreateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/employee';

    public $css = [
        'create.css',
    ];
    public $js = [
        'create.js'
    ];
}
