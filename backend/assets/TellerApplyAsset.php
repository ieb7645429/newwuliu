<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerApplyAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
        'apply.js'
    ];

}
