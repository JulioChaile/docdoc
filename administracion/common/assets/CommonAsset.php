<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class CommonAsset extends AssetBundle
{
    public $sourcePath = '@common';
    public $css = [
    ];
    public $js = [
        'js/main.js',
        'js/VueDirectives.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\BowerAsset',
        'yii\widgets\MaskedInputAsset',
    ];
}
