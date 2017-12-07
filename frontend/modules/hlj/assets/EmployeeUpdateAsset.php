<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EmployeeUpdateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/employee';

    public $css = [
        'create.css',
    ];
    public $js = [
        'update.js'
    ];
}
