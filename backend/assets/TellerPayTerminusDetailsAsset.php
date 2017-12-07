<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerPayTerminusDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
            'pay-terminus-details.js'
    ];

}
