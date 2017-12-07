<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MemberPayableAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/member-pay';

    public $css = [
        'member-payable.css'
    ];
    public $js = [
            'member-payable.js'
    ];
}
