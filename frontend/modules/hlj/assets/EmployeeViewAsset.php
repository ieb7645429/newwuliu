<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class EmployeeViewAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/employee';

    public $js = [
        'view.js'
    ];
    public $depends = [
            'components\Lodop\HeilongjiangAsset'
    ];
}
