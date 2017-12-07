<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MemberCreateOrderAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/member';

    public $css = [
            'create_order.css'
    ];
    public $js = [
        'create_order.js'
    ];
}
