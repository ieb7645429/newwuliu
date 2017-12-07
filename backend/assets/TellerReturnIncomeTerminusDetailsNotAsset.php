<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerReturnIncomeTerminusDetailsNotAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
            'return-income-terminus-details-not.js'
    ];

}
