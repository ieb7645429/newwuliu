<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerReturnIncomeDealerDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
            'return-income-dealer-details.js',
		    'income-driver-details-print.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
