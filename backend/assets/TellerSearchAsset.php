<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerSearchAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
        'search.js'
    ];

}
