<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CreateUserSearchAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/create-user';

    public $js = [
        'search.js'
    ];

}
