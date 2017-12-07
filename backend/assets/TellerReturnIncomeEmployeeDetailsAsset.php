<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerReturnIncomeEmployeeDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
            'return-income-employee-details.js',
			'income-driver-details-print.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}