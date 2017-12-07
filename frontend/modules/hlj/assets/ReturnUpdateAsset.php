<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnUpdateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/return';

    public $css = [
            'create.css'
    ];
    public $js = [
        'update.js'
    ];
    public $depends = [
            'components\Lodop\HeilongjiangAsset'
    ];
}
