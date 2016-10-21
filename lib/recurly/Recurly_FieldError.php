<?php
namespace app\lib\recurly;
/**
 * Exception class used by the Recurly PHP Client.
 *
 * @category   Recurly
 * @package    Recurly_Client_PHP
 * @copyright  Copyright (c) 2011 {@link http://recurly.com Recurly, Inc.}
 */


class Recurly_FieldError
{
  var $field;
  var $symbol;
  var $description;
  
  public function __toString() {
    if (!empty($this->field) && ($this->__readableField() != 'base')) {
      return $this->__readableField() . ' ' . $this->description;
    }
    else {
      return $this->description;
    }
  }
  
  private function __readableField() {
    if (empty($this->field))
      return null;

    $pos = strrpos($this->field, '.');
    if ($pos === false)
      return str_replace('_', ' ', $this->field);
    else
      return str_replace('_', ' ', substr($this->field, $pos + 1));
  }
}
