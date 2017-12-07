<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class UserResetPasswordAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/user';

    public $js = [
        'resetpassword.js'
    ];
}
