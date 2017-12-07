<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SendAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/send';

    public $css = [
            'index.css',
    ];

    public $js = [
            'index.js'
    ];

    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}