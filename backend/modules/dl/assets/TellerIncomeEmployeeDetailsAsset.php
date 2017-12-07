<?php

namespace backend\modules\dl\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerIncomeEmployeeDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/dl/views/teller';
    public $js = [
        'income-employee-details.js'
    ];}
