<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ResetPasswordAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/site';

    public $js = [
        'resetpassword.js'
    ];
}
