<?php

namespace backend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerIncomeLogisticssnLogAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/dl/views/teller';

    public $js = [
            'income-logisticssn-log.js'
    ];
    
    public $depends = [
        'components\Lodop\LodopAsset'
    ];

}
