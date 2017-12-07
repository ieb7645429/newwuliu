<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ReturnViewAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/return';

    public $js = [
        'view.js'
    ];
    public $depends = [
            'components\Lodop\HeilongjiangAsset'
    ];
}
