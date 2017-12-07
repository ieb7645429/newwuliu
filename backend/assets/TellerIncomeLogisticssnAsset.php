<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerIncomeLogisticssnAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
            'income-logisticssn.js'
    ];

    public $depends = [
        'components\Lodop\LodopAsset'
    ];
}
