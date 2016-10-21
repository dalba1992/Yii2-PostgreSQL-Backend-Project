<?php

namespace app\widgets\chart;

use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class DonutChart extends Widget {

    public $id;

    public $clientOptions;

    public $dataSet;

    public function init() {
        parent::init();
        if(!isset($this->clientOptions['width'])) {
            $this->clientOptions['width'] = 300;
        }
        if(!isset($this->clientOptions['height'])) {
            $this->clientOptions['height'] = 300;
        }
        if(!isset($this->clientOptions['donutWidth'])) {
            $this->clientOptions['donutWidth'] = 75;
        }
        if(!isset($this->clientOptions['legendRectSize'])) {
            $this->clientOptions['legendRectSize'] = 18;
        }
        if(!isset($this->clientOptions['legendSpacing'])) {
            $this->clientOptions['legendSpacing'] = 4;
        }

        if($this->id === null) {
            $this->id = $this->getId();
        }
    }

    public function run() {

        $view = $this->getView();

        $jsBegin = "
            (function(d3) {
                'use strict';
        ";
        $jsEnd = "
            })(window.d3);
        ";
        $javascript = '';

        $javascript .= "var dataset = " . Json::htmlEncode($this->dataSet) . ";";
        $javascript .= "
            var width = ". Json::htmlEncode($this->clientOptions['width']) .";
            var height = ". Json::htmlEncode($this->clientOptions['height']) .";
            var radius = Math.min(width, height) / 2;
            var donutWidth = ". Json::htmlEncode($this->clientOptions['donutWidth']) .";
            var legendRectSize = ". Json::htmlEncode($this->clientOptions['legendRectSize']) .";
        	var legendSpacing = ". Json::htmlEncode($this->clientOptions['legendSpacing']) .";
        ";


        $view->registerJs($jsBegin . $javascript . $jsEnd, $view::POS_END);
    }
}