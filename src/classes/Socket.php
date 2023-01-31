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

  private bool $in_bulk = false;
  private array $bulk_list = [];


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
   *
   * @return array|bool  False on failure.
   *                     Array on success.
   *                     True on success if $mode is MPD_CMD_READ_BOOL
   * @link https://mphpd.org/doc/methods/cmd
   */
  public function cmd(string $command, array $params = [], int $mode = MPD_CMD_READ_NORMAL)
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

    $parsed = parse($this->readls(), $mode);
    if($parsed instanceof MPDException){
      return $this->set_error($parsed);
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
   * Function to start a command-list.
   * @return void
   * @tags commandlist
   */
  public function bulk_start()
  {
    $this->in_bulk = true;
    $this->bulk_list = [];
  }


  /**
   * Function to end a command-list and execute its commands
   * The command list is stopped in case an error occurs.
   * @return array|false Returns an array containing the commands responses.
   * @tags commandlist
   */
  public function bulk_end(): array
  {

    // if there is no command list return false
    if($this->in_bulk === false){ return false; }

    // start the command list at protocol level (without reading the non-existent response)
    $this->cmd("command_list_ok_begin", [], MPD_CMD_READ_NONE);

    // then send every command. Again without reading anything.
    $ret = [];
    foreach($this->bulk_list as $b){
      $this->cmd($b["cmd"], [], MPD_CMD_READ_NONE);
    }

    // end and execute the command list
    $this->cmd("command_list_end", [], MPD_CMD_READ_NONE);

    // then read the response from each command
    // we can do this because readls() stops on OK,ACK and list_OK
    $f_err = false;
    foreach($this->bulk_list as $b){
      $parsed = parse($this->readls(), $b["mode"]);

      if($parsed instanceof MPDException AND $b["mode"] === MPD_CMD_READ_BOOL){
        $ret[] = false;
      }else{
        $ret[] = $parsed;
      }

      // if there is an error -> stop
      if($parsed instanceof MPDException){
        $this->set_error($parsed);
        $f_err = true;
        break;
      }
    }

    // There is a remaining "OK\n" in the socket buffer if all commands in the command list succeeded.
    // We need to read that before sending other commands. If we would not do that the next commands response
    // would be empty as there was still an "OK\n" in the buffer.
    // We only need to do this if the command list did NOT fail!
    if(!$f_err){
      $lb = "";
      $this->readl($lb);
    }

    // disable bulk and return
    $this->in_bulk = false;
    return $ret;
  }


  /**
   * Function to abort the current command list.
   * We can do that because we only start the list at protocol level when bulk_end() is called.
   * @return void
   * @tags commandlist
   */
  public function bulk_abort()
  {
    $this->bulk_list = [];
    $this->in_bulk = false;
  }


  /**
   * Function to add a command to the bulk_list.
   * @see MphpD::cmd()
   * @param string $cmd
   * @param array $params
   * @param int $mode
   * @return bool
   * @tags commandlist
   */
  public function bulk_add(string $cmd, array $params = [], int $mode = MPD_CMD_READ_BOOL) : bool
  {
    // return false if bulk is not enabled
    if(!$this->in_bulk) {
      return false;
    }

    $cmd = $cmd.escape_params($params);
    $this->bulk_list[] = [
      "cmd" => $cmd,
      "mode" => $mode
    ];

    return true;
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
   * Send the password for authentication
   * @return bool
   */
  private function password(string $password)
  {
    return $this->cmd("password", [$password], MPD_CMD_READ_BOOL);
  }


  /**
   * Ping.
   * @return bool
   */
  public function ping()
  {
    return $this->cmd("ping", [], MPD_CMD_READ_BOOL);
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
   * Return a list of all available tag types.
   * @return array|false
   */
  public function tagtypes()
  {
    return $this->cmd("tagtypes", [], MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Disable specified tag types.
   * @param array $tagtypes A list of tag types to disable.
   * @return bool
   */
  public function tagtypes_disable(array $tagtypes) : bool
  {
    return $this->cmd("tagtypes disable", $tagtypes, MPD_CMD_READ_BOOL);
  }


  /**
   * Enable specified tag types.
   * @param array $tagtypes A list of tag types to enable.
   * @return bool
   */
  public function tagtypes_enable(array $tagtypes) : bool
  {
    return $this->cmd("tagtypes enable", $tagtypes, MPD_CMD_READ_BOOL);
  }


  /**
   * Remove all tag types from responses.
   * @return bool
   */
  public function tagtypes_clear() : bool
  {
    return $this->cmd("tagtypes clear", [], MPD_CMD_READ_BOOL);
  }


  /**
   * Enable all available tag types.
   * @return bool
   */
  public function tagtypes_all() : bool
  {
    return $this->cmd("tagtypes all", [], MPD_CMD_READ_BOOL);
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
  private function readl(string &$lb): int
  {
    $lb = "";
    if(($lb = fgets($this->socket)) === false){
      return 0;
    }

    if(str_starts_with($lb, "binary:")){

      $p = parse([$lb]);

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
  private function readls(): array
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

}