<?php

namespace frontend\modules\hlj\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MemberUpdateAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/hlj/views/member';

    public $css = [
        'create_order.css'
    ];
    public $js = [
        'update.js'
    ];
}
