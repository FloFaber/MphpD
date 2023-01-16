<?php

namespace FloFaber;

use Exception;
use Throwable;

/**
 * MPDException is a slightly modified version of a standard Exception.
 * You may call `MPDException::getCode` and `MPDException::getMessage` to retrieve information about the error.
 *
 * In case an error occurs at the protocol level and depending on the errormode you chose the called methods either throw an MPDException or simply return false.
 * Either way you want to know what went wrong.
 *
 * Available Error-modes are described [here](/doc/general/configuration#errormode).
 *
 * To retrieve the last occurred error call [MphpD::getError](/doc/methods/mphpd-getError).
 *
 * @title MPDException
 * @usage MphpD::getError() : MPDException
 */
class MPDException extends Exception
{

  private int $commandlist_num = 0;
  private string $command = "";

  public function __construct($message = "", $code = 0, Throwable $previous = null, string $command = "", int $commandlist_num = 0)
  {

    $this->command = $command;
    $this->commandlist_num = $commandlist_num;

    parent::__construct($message, $code, $previous);
  }


  /**
   * Returns all information as string
   * @return string
   */
  public function __toString()
  {
    return __CLASS__ . ": [$this->code];$this->message;$this->command;$this->commandlist_num\n";
  }


  /**
   * Returns the command which caused the error.
   * @return string
   */
  public function getCommand() : string
  {
    return $this->command;
  }


  /**
   * Returns the command's list-number in case a [commandlist](/doc/general/commandlist) was used.
   * @return int
   */
  public function getCommandlistNum() : int
  {
    return $this->commandlist_num;
  }

}