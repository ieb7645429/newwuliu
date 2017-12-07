<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class BalanceLogBalanceAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/balance-log';

    public $css = [
        'balance.css'
    ];
    public $js = [
            'balance.js'
    ];
}
