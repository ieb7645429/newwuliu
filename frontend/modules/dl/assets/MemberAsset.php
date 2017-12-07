<?php

namespace frontend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MemberAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/dl/views/member';
    
    public $js = [
        'index.js'
    ];
    public $depends = [
            'components\Lodop\DalianAsset'
    ];
}
