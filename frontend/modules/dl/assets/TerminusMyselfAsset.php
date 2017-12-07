<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class TerminusMyselfAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/terminus';

    public $css = [
        'index.css',
    ];
    
    public $js = [
        'myself.js'
    ];
    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}
