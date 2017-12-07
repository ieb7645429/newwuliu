<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnCreateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/return';

    public $css = [
            'create.css'
    ];
    public $js = [
        'create.js'
    ];
    public $depends = [
            'components\Lodop\HeilongjiangAsset'
    ];
}
