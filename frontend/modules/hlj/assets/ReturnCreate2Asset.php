<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnCreate2Asset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/return';

    public $css = [
            'create.css'
    ];
    public $js = [
        'create2.js'
    ];
    public $depends = [
            'components\Lodop\HeilongjiangAsset'
    ];
}
