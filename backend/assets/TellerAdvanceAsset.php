<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerAdvanceAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
        'advance.js'
    ];

}
