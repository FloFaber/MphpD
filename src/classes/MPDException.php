<?php

namespace FloFaber;

use Exception;
use Throwable;

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

  public function __toString()
  {
    return __CLASS__ . ": [$this->code];$this->message;$this->command;$this->commandlist_num\n";
  }

  public function getCommand() : string
  {
    return $this->command;
  }

  public function getCommandlistNum() : int
  {
    return $this->commandlist_num;
  }

}