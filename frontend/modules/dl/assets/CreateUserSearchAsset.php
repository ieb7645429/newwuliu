<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CreateUserSearchAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/create-user';

    public $js = [
        'search.js'
    ];

}
