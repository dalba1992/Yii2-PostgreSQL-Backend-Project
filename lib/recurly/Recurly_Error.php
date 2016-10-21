<?php
namespace app\lib\recurly;
use Exception;
/**
 * Exception class used by the Recurly PHP Client.
 *
 * @category   Recurly
 * @package    Recurly_Client_PHP
 * @copyright  Copyright (c) 2011 {@link http://recurly.com Recurly, Inc.}
 */
class Recurly_Error extends Exception {}
class Recurly_NotFoundError extends Recurly_Error {}
class Recurly_UnauthorizedError extends Recurly_Error {}
class Recurly_ConfigurationError extends Recurly_Error {}
class Recurly_ConnectionError extends Recurly_Error {}
class Recurly_RequestError extends Recurly_Error {}
class Recurly_ApiRateLimitError extends Recurly_RequestError {}
class Recurly_ForgedQueryStringError extends Recurly_Error {}
class Recurly_ServerError extends Recurly_Error {}

