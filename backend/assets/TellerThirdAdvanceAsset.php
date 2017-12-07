<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerThirdAdvanceAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
        'third-advance.js'
    ];

}
