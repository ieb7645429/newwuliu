<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ResetPasswordAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/site';

    public $js = [
        'resetpassword.js'
    ];
}
