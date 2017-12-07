<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerOrderAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
        'order.js'
    ];

}
