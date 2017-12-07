<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class InstockHistoryAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/instock';

    public $css = [
            'history-list.css'
    ];
}
