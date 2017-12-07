<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EmployeeEditAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/employee';

    public $js = [
        'view.js'
    ];
    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}
