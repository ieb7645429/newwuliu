<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MemberUpdateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/member';

    public $css = [
        'create_order.css'
    ];
    public $js = [
        'update.js'
    ];
}
