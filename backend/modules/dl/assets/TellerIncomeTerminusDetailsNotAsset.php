<?php

namespace backend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerIncomeTerminusDetailsNotAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/dl/views/teller';

    public $js = [
            'income-terminus-details-not.js'
    ];

}
