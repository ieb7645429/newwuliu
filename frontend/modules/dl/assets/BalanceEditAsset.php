<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class BalanceEditAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/balance-edit';

    public $js = [
        'update.js'
    ];
    
    public $css = [
        'update.css'
    ];
}
