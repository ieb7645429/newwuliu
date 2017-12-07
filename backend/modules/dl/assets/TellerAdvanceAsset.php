<?php

namespace backend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerAdvanceAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/dl/views/teller';

    public $js = [
        'advance.js'
    ];

}
