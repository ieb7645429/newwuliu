<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class UserResetPasswordAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/user';

    public $js = [
        'resetpassword.js'
    ];
}
