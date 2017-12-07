<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class BalanceEditAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/balance-edit';

    public $js = [
        'update.js'
    ];
    
    public $css = [
        'update.css'
    ];
}
