<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MemberAsset extends AssetBundle
{
    public $sourcePath = '@frontend/views/member';
    
    public $js = [
        'index.js'
    ];
    public $depends = [
            'components\Lodop\LodopAsset'
    ];
}
