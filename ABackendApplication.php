<?php
/**
 * @link http://www.east-wolf.com/
 *
 * @copyright Copyright (c) 2015 Trendy Butler
 */

namespace app\web;

use Yii;
use yii\base\InvalidRouteException;

/**
 * Application is the base class for all web application classes.
 */
class ABackendApplication extends \yii\web\Application
{
    public $version = null;
}
