<?php

namespace app\widgets\chart;

use yii\web\AssetBundle;
use yii\web\View;

class ChartAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'web/js/d3.min.js',
        'web/js/d3.tip.v0.6.3.js',
    ];

    public $jsOptions =[
        'position' => View::POS_END,
    ];
}