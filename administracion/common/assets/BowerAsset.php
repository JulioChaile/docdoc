<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BowerAsset extends AssetBundle
{
    public $sourcePath = '@bower/';
    public $css = [
        'font-awesome/css/font-awesome.min.css',
    ];
    public $js = [
        'vue/dist/vue.js',
        'bootstrap-confirmation2/bootstrap-confirmation.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'kartik\select2\Select2Asset',
        'kartik\select2\ThemeDefaultAsset',
    ];
}
