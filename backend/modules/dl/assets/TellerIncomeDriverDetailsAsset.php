<?php

namespace backend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerIncomeDriverDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/dl/views/teller';

    public $js = [
            'income-driver-details.js',
			'income-driver-details-print.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
