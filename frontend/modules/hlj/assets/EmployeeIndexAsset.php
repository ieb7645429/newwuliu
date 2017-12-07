<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EmployeeIndexAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/employee';

    public $css = [
            'index.css'
    ];
    public $js = [
        'index.js'
    ];
}
