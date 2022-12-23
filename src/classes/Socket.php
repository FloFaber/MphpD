<?php

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

  protected int $errormode = MPD_ERRORMODE_EXCEPTION;

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
    if(isset($options["errormode"])){
      $this->errormode = $options["errormode"];
    }
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
   * @param string $command The command
   * @param array $params Parameters, automatically escaped
   * @param int $mode One of the following:
   *
   *                  * MPD_CMD_READ_NONE        - Do not read anything from the answer. Returns an empty array.
   *
   *                  * MPD_CMD_READ_NORMAL      - Parses the answer as a one-dimensional "key=>value" array.
   *                                               If a key already existed its value gets overwritten.
   *                                               Used for commands like "status" where only unique keys are given.
   *
   *                  * MPD_CMD_READ_LIST          - Parses the answer as a list of "key=>value" arrays.
   *                                               Used for commands like "listplaylists" where keys are not unique.
   *
   *                  * MPD_CMD_READ_LIST_SINGLE - Parses the answer into a simple "indexed" array.
   *                                               Used for commands like "idle" where there is
   *                                               only a single possible "key".
   *
   *                  * MPD_CMD_READ_RAW         - @ToDo: Reads the raw response.
   * @return array|false False on failure.
   *                     Array on success.
   * @throws MPDException
   * @link https://mphpd.org/doc/methods/cmd
   */
  public function cmd(string $command, array $params = [], int $mode = MPD_CMD_READ_NORMAL)
  {

    if(!$this->connected){
      $this->setError("Socket not connected!");
      return false;
    }

    $cmd = $command.escape_params($params);

    if (fputs($this->socket, "$cmd\n") === false) {
      $this->setError("Unable to write to socket!");
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
      return $this->setError($parsed);
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
   */
  public function bulk_start()
  {
    $this->in_bulk = true;
    $this->bulk_list = [];
  }


  /**
   * Function to end a command-list and execute its commands
   * The command list is stopped in case an error occurs.
   * @return array|false Returns an array containing the command's response.
   * @throws MPDException
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
      $ret[] = $parsed;

      // if there is an error -> stop
      if($parsed instanceof MPDException){
        $f_err = true;
        break;
      }
    }

    // There is a remaining "OK\n" in the socket buffer if all command in the command list succeeded.
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
   */
  public function bulk_add(string $cmd, array $params = [], int $mode = MPD_CMD_READ_NORMAL) : bool
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
   * @throws MPDException
   */
  public function close()
  {
    return $this->cmd("close");
  }


  /**
   * @throws MPDException
   */
  public function kill()
  {
    return $this->cmd("kill");
  }


  /**
   * @throws MPDException
   */
  private function password(string $password)
  {
    return $this->cmd("password", [$password]);
  }


  /**
   * @throws MPDException
   */
  public function ping()
  {
    return $this->cmd("ping");
  }


  /**
   * Sets the max. binary response size to $limit bytes for the current connection.
   * @param int $limit
   * @return bool
   * @throws MPDException
   */
  private function set_binarylimit(int $limit) : bool
  {
    return $this->cmd("binarylimit", [$limit]) !== false;
  }

  /**
   * Returns the current binarylimit
   * @return int
   */
  public function binarylimit() : int
  {
    return $this->binarylimit;
  }


  /**
   * @throws MPDException
   */
  public function tagtypes()
  {
    return $this->cmd("tagtypes", [], MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * @throws MPDException
   */
  public function tagtypes_disable(array $tagtypes)
  {
    return $this->cmd("tagtypes disable", $tagtypes);
  }


  /**
   * @throws MPDException
   */
  public function tagtypes_enable(array $tagtypes)
  {
    return $this->cmd("tagtypes enable", $tagtypes);
  }


  /**
   * @throws MPDException
   */
  public function tagtypes_clear()
  {
    return $this->cmd("tagtypes clear");
  }


  /**
   * @throws MPDException
   */
  public function tagtypes_all()
  {
    return $this->cmd("tagtypes all");
  }



  /**
   * @throws MPDException
   */
  public function setError($err): bool
  {
    if(!$err instanceof MPDException){
      $this->last_error = parse_error($err);
    }else{
      $this->last_error = $err;
    }

    return error($this->last_error, $this->errormode);
  }


  public function getError(): MPDException
  {
    return $this->last_error;
  }


  /**
   * Initiate connection to MPD with the parameters given at instantiation.
   * @throws MPDException
   * @return bool
   */
  public function connect() : bool {

    if($this->socket_type === "unix"){
      $address = $this->host;
    }else{
      $address = "tcp://$this->host:$this->port";
    }

    $this->socket = stream_socket_client($address, $errno, $errstr, $this->timeout);

    if(!$this->socket){
      return $this->setError(new MPDException($errstr, $errno));
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
          return $this->setError(new MPDException("Error setting binarylimit in MPD!", 2));
        }
      }else{
        $this->binarylimit = 8192; // 8192 bytes is hardcoded in MPD before version 0.22.4.
      }

      // nevertheless set the socket chunk size to the specified limit.
      if(!stream_set_chunk_size($this->socket, $this->binarylimit)){
        return $this->setError(new MPDException("Error setting socket chunk size!", 1));
      }
    }

    return true;
  }


  public function disconnect(){
    try{
      $this->close();
    } catch (MPDException $e) {
      $this->connected = false;
    }
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
  public function readls(): array
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