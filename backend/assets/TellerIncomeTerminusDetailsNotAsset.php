<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerIncomeTerminusDetailsNotAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
            'income-terminus-details-not.js'
    ];

}
