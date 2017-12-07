<?php

namespace components\autocomplete;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AutoCompltetAsset extends AssetBundle
{
    public $sourcePath = '@components/autocomplete';
    
    public $js = [
        'autocomplete.js',
    ];

    public $css = [
        'autocomplete.css'
    ];
}
