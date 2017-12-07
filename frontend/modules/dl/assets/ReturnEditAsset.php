<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnEditAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/return';

    public $js = [
        'view.js'
    ];
    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}
