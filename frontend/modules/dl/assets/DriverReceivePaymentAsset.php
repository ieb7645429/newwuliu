<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class DriverReceivePaymentAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/driver';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'receive-payment.js'
    ];

}
