<?php
/*
 * MphpD
 * http://mphpd.org
 *
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

namespace FloFaber;

require_once __DIR__ . "/../inc/utils.php";

class Socket
{

  /**
   * @var $socket Resource
   */
  private $socket;
  private MPDException $last_error;

  private string $version;
  public bool $connected = false;
  protected string $socket_type = "";
  protected string $host = "127.0.0.1";
  protected int $port = 6600;
  protected string $password = "";
  protected int $binarylimit = 8192;
  protected int $timeout = 1;


  /*
   * #########################
   *    CONNECTION SETTINGS
   * #########################
   */

  public function __construct(array $options = [])
  {
    if(isset($options["host"])){
      $this->host = $options["host"];
    }
    if(isset($options["port"])){
      $this->port = $options["port"];
    }
    if(isset($options["password"])){
      $this->password = $options["password"];
    }
    if(isset($options["binarylimit"]) AND is_numeric($options["binarylimit"])){
      $this->binarylimit = $options["binarylimit"];
    }
    if(isset($options["timeout"]) AND is_numeric($options["timeout"])){
      $this->timeout = $options["timeout"];
    }
    if(str_starts_with($this->host, "unix:")){
      $this->socket_type = "unix";
      $this->host = substr($this->host, 5);
    }

  }


  /**
   * Send $command with $params to the MPD server.
   *
   * You, the library's user, are not intended to ever
   * need this method. If you ever need it because the library does not support
   * a specific command please file a [bug report](https://github.com/FloFaber/MphpD/issues).
   * This method also parses MPDs response depending on the chosen mode.
   *
   * @param string $command The command
   * @param array $params Parameters, automatically escaped
   * @param int $mode One of the following constants:
   *
   *                  * MPD_CMD_READ_NONE        - Do not read anything from the answer. Returns an empty array.
   *
   *                  * MPD_CMD_READ_NORMAL      - Parses the answer as a one-dimensional "key=>value" array.
   *                                               If a key already existed its value gets overwritten.
   *                                               Used for commands like "status" where only unique keys are given.
   *
   *                  * MPD_CMD_READ_LIST        - Parses the answer as a list of "key=>value" arrays.
   *                                               Used for commands like "listplaylists" where keys are not unique.
   *
   *                  * MPD_CMD_READ_LIST_SINGLE - Parses the answer into a simple "indexed" array.
   *                                               Used for commands like "idle" where there is
   *                                               only a single possible "key".
   *
   *                  * MPD_CMD_READ_BOOL        - Parses the answer into `true` on OK and list_OK and `false` on `ACK`.
   *                                               Used for commands which do not return anything but OK or ACK.
   *
   * @param array $list_start In combination with `$mode = MPD_CMD_READ_LIST` indicates on which `key` a new list starts.
   * @return array|bool  False on failure.
   *                     Array on success.
   *                     True on success if $mode is MPD_CMD_READ_BOOL
   * @link https://mphpd.org/doc/methods/cmd
   */
  public function cmd(string $command, array $params = [], int $mode = MPD_CMD_READ_NORMAL, array $list_start = [])
  {

    if(!$this->connected){
      $this->set_error("Socket not connected!");
      return false;
    }

    $cmd = $command.escape_params($params);

    if (fputs($this->socket, "$cmd\n") === false) {
      $this->set_error("Unable to write to socket!");
      return false;
    }

    if($command === "close"){
      $this->connected = false;
      return [];
    }

    if($mode === MPD_CMD_READ_NONE){
      return [];
    }

    $parsed = $this->parse($this->readls(), $mode, $list_start);
    if($parsed instanceof MPDException){
      return $this->set_error($parsed);
    }

    $metadata = stream_get_meta_data($this->socket);
    if($metadata["timed_out"]){
      return $this->set_error("Connection timed out");
    }

    return $parsed;

  }


  public function get_socket()
  {
    return $this->socket;
  }


  /**
   * Returns MPDs version as string
   * @return string
   */
  public function get_version() : string
  {
    return $this->version;
  }


  /**
   * Function to compare a given version string with the current version of MPD
   * @param string $version
   * @return bool Returns true if MPDs version is equal to or newer than the given version. False otherwise.
   */
  public function version_bte(string $version): bool
  {
    return version_compare($this->get_version(), $version) >= 0;
  }


  /**
   * Waits until there is a noteworthy change in one or more of MPD’s subsystems.
   * @param string $subsystem
   * @param int $timeout Specifies how long to wait for MPD to return an answer.
   * @return array|false Returns an array of changed subsystems or false on timeout.
   */
  public function idle(string $subsystem = "", int $timeout = 60)
  {
    stream_set_timeout($this->get_socket(), $timeout);
    $subsystems = $this->cmd("idle", [$subsystem], MPD_CMD_READ_LIST_SINGLE);


    // if the stream timed out we need to send "noidle". Otherwise, MPD won't know that we don't want to wait anymore.
    // Alternatively reconnecting would also work.
    $metadata = stream_get_meta_data($this->get_socket());
    if($metadata["timed_out"]){
      $this->cmd("noidle");
    }

    return $subsystems;
  }


  /**
   * Close the connection to the MPD socket
   * @return void
   */
  public function close() : void
  {
    $this->cmd("close", [], MPD_CMD_READ_NONE);
  }


  /**
   * Kill MPD.
   * @return void
   */
  public function kill() : void
  {
    $this->cmd("kill", [], MPD_CMD_READ_NONE);
  }


  /**
   * Sets the max. binary response size to $limit bytes for the current connection.
   * @param int $limit
   * @return bool
   */
  private function set_binarylimit(int $limit) : bool
  {
    return $this->cmd("binarylimit", [$limit], MPD_CMD_READ_BOOL);
  }


  /**
   * Returns the current binarylimit
   * @return int
   */
  public function get_binarylimit() : int
  {
    return $this->binarylimit;
  }


  /**
   * Send the password for authentication
   * @return bool
   */
  private function password(string $password)
  {
    return $this->cmd("password", [$password], MPD_CMD_READ_BOOL);
  }


  /**
   * Function to set the last occurred error.
   * Should only be used inside the library!
   * @param MPDException|string $err
   * @return false
   */
  public function set_error($err) : bool
  {
    if(!$err instanceof MPDException){
      $this->last_error = parse_error($err);
    }else{
      $this->last_error = $err;
    }
    return false;
  }


  /**
   * Return an array containing information about the last error
   * @return array associative array containing the following keys:
   * <pre>
   * [
   *   "code" => (int),
   *   "message" => (string),
   *   "command" => (string),
   *   "commandlistnum" => (int)
   * ]
   * </pre>
   */
  public function get_last_error() : array
  {
    if(!isset($this->last_error)){
      return [];
    }
    return [
      "code" => $this->last_error->getCode(),
      "message" => $this->last_error->getMessage(),
      "command" => $this->last_error->getCommand(),
      "commandlistnum" => $this->last_error->getCommandlistNum()
    ];
  }


  /**
   * Initiate connection to MPD with the parameters given at instantiation.
   * @throws MPDException Throws MPDException when a connection error occurs.
   * @return bool
   */
  public function connect() : bool
  {

    if($this->socket_type === "unix"){
      $address = $this->host;
    }else{
      $address = "tcp://$this->host:$this->port";
    }

    $this->socket = @stream_socket_client($address, $errno, $errstr, $this->timeout);

    if(!$this->socket){
      throw new MPDException($errstr, $errno);
    }

    // set socket timeout
    stream_set_timeout($this->socket, $this->timeout);

    $helo = "";
    $this->readl($helo);

    if(str_starts_with($helo, "OK MPD")){
      $this->version = trim(str_replace("OK MPD", "", $helo));
      $this->connected = true;
    }

    if(!empty($this->password) && $this->password($this->password) === false){
      $this->connected = false;
      return false;
    }

    // if binarylimit is set we need to set it on the client side as well as on the server side.
    if($this->binarylimit){
      // binarylimit is only supported in MPD 0.22.4 and above
      if($this->version_bte("0.22.4")){
        if($this->set_binarylimit($this->binarylimit) !== true){
          return $this->set_error(new MPDException("Error setting binarylimit in MPD!", 2));
        }
      }else{
        $this->binarylimit = 8192; // 8192 bytes is hardcoded in MPD before version 0.22.4.
      }

      // nevertheless set the socket chunk size to the specified limit.
      if(!stream_set_chunk_size($this->socket, $this->binarylimit)){
        return $this->set_error(new MPDException("Error setting socket chunk size!", 1));
      }
    }

    return true;
  }


  /**
   * Disconnect from MPD
   * @return void
   */
  public function disconnect()
  {
    $this->close();
    $this->connected = false;
  }


  /**
   * Function to read only a single line. Stops on '\n', 'OK', 'ACK' and 'list_OK'
   * @param string $lb "Line Buffer"
   * @return int  0 => Failure
   *
   *              1 => OK, but keep going
   *
   *              2 => Binary Response
   *
   *              3 => OK or ACK reached, stop.
   */
  protected function readl(string &$lb): int
  {
    $lb = "";
    if(($lb = fgets($this->socket)) === false){
      return 0;
    }

    if(str_starts_with($lb, "binary:")){

      $p = $this->parse([$lb]);

      if($p instanceof MPDException){
        return 0;
      }

      $binary = (int) $p["binary"];
      if($binary > 0){
        $lb = fread($this->socket, $binary);
        return 2;
      }elseif($binary === 0){
        fread($this->socket, 1); // read newline
      }

    }

    // return stop code if that was all to read
    if(str_starts_with($lb, "OK") OR
       str_starts_with($lb, "ACK") OR
       str_starts_with($lb, "list_OK"))
    {
      return 3;
    }

    return 1;
  }


  /**
   * Read multiple lines from socket
   * @return array
   */
  protected function readls(): array
  {
    $lines = [];
    $lb = "";

    while(true){
      $l = $this->readl($lb);
      if($l === 0){ break; }
      if($l === 2){
        $lines[] = "binary_data:".$lb;
      }else{
        $lines[] = trim($lb);
      }
      if($l === 3){ break; }
    }

    return $lines;
  }


  /**
   * Parse an array of lines returned by MPD into a PHP array.
   * @param array $lines Given lines
   * @param int $mode Refer to `cmd()`'s documentation.
   * @param array $list_starts An array of possible keys on which a new list-item starts.
   * @return array|true|MPDException
   */
  protected function parse(array $lines, int $mode = MPD_CMD_READ_NORMAL, array $list_starts = [])
  {

    $b = [];
    $tmp = [];
    $first_key = NULL;

    $lines_parsed = [];

    // parse lines first so we can look ahead later
    foreach($lines as $line){
      if(str_starts_with($line, "ACK")){
        return parse_error($line);
      }
      $ls = explode(":", $line, 2);
      if(count($ls) < 2){
        continue;
      }
      if($ls[0] === "binary_data"){
        $k = "binary_data";
        $v = $ls[1];
      }else{
        $k = strtolower(trim($ls[0]));
        $v = trim($ls[1]);
        if(is_numeric($v)){
          $v = (int)$v;
        }
      }
      $lines_parsed[] = [ "k" => $k, "v" => $v ];
    }

    // now loop through the parsed lines
    for($i = 0; $i < count($lines_parsed); $i++){

      $line = $lines_parsed[$i];
      $k = $line["k"];
      $v = $line["v"];

      // push to current key-value pair to a tmp array which we later will at to the list of items.
      $tmp[$k] = $v;

      // on the last key (the one after the first key) we push the stored data to the result array
      // Example:
      // channel: test    <- First key
      // message: hi      <- The last key
      // channel: test    <- Also the "first" key
      // message: hello
      // channel: test
      // message: goodbye
      // OK


      // omfg
      // ok,ok. If we read a list and the `key` of the next line is either in `$list_starts` or is equal to `$first_key` we push the list-item to the list of items.
      if($mode === MPD_CMD_READ_LIST &&
        (//$first_key !== NULL &&
          (isset($lines_parsed[$i+1]) && ($lines_parsed[$i+1]["k"] === $first_key || in_array($lines_parsed[$i+1]["k"], $list_starts))) || !isset($lines_parsed[$i+1])
        )){

        $b[] = $tmp;
        $tmp = [];

        // If we only parse a list with a single possible key just push its value to the result array.
        // If this is used in a command which returns more than one possible key the result will look a little funky.
        // We can, however, simple blame the user.
      }elseif($mode === MPD_CMD_READ_LIST_SINGLE){
        $b[] = $v;
      }

      // set the first encountered key if there isn't already one
      if(($mode === MPD_CMD_READ_LIST || $mode === MPD_CMD_READ_LIST_SINGLE) && $first_key === NULL){
        $first_key = $k;
      }

    }

    if($mode === MPD_CMD_READ_BOOL){
      return true;
    }

    return ($mode === MPD_CMD_READ_LIST || $mode === MPD_CMD_READ_LIST_SINGLE ? $b : $tmp);
  }

}