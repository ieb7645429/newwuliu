<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class PrintAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/print';


    public $depends = [
            'components\Lodop\PrintAsset'
    ];
}
