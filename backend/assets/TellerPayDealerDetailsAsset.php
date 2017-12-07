<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerPayDealerDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
            'pay-dealer-details.js'
    ];

}
