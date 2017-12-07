<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class Nationwide extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';

    public $js = [
        'nationwide.js'
    ];

}
