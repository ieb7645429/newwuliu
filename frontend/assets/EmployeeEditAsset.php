<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EmployeeEditAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/employee';

    public $js = [
        'view.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
