<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerReturnPayTerminusDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
        'return-pay-terminus-details.js'
    ];

}
