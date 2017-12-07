<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnEditAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/return';

    public $js = [
        'view.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
