<?php
namespace app\lib\recurly;
/**
 * Exception class used by the Recurly PHP Client.
 *
 * @category   Recurly
 * @package    Recurly_Client_PHP
 * @copyright  Copyright (c) 2011 {@link http://recurly.com Recurly, Inc.}
 */


class Recurly_ValidationError extends Recurly_Error
{
  var $object;
  var $errors;
  
  function __construct($message, $object, $errors) {
    $this->object = $object;
    $this->errors = $errors;

    // Create a better error message
    $errs = array();
    foreach ($errors as $err) {
      if ($err instanceof Recurly_TransactionError) {
        # Return just the customer message from the transaction error
        parent::__construct($err->customer_message);
		return;
      }
      else
        $errs[] = strval($err);
    }
    $message = ucfirst(implode($errs, ', '));
    if (substr($message, -1) != '.')
      $message .= '.';
	 
	return $message;
    //parent::__construct($message);
  }
}