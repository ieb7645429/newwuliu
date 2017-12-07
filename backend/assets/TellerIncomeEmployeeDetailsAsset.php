<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class TellerIncomeEmployeeDetailsAsset extends AssetBundle
{
    public $sourcePath = '@backend/views/teller';
    public $js = [
        'income-employee-details.js'
    ];}
