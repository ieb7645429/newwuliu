<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class BalanceLogBalanceAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/balance-log';

    public $css = [
        'balance.css'
    ];
    public $js = [
            'balance.js'
    ];
}
