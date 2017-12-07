<?php

namespace frontend\modules\hlj;
use Yii;
/**
 * hlj module definition class
 */
class HLJ extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'frontend\modules\hlj\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        \Yii::configure($this, require(__DIR__ . '/config.php'));
        
        
    }
}
