<?php

namespace FloFaber;

require_once __DIR__ . "/../classes/MPDException.php";

// php<8 compatibility
if(!function_exists("str_starts_with")){
  function str_starts_with(string $haystack, string $needle) : bool
  {
    return strpos($haystack, $needle) === 0;
  }
}


/**
 * Function to parse an array of given parameters.
 * @param array $params Array of strings.
 * @param int $flags One or multiple OR-ed together of the following:
 *                  * MPD_ESCAPE_NORMAL        - "beetle's juice" becomes "\"beetle\'s juice\""
 *                  * MPD_ESCAPE_DOUBLE_QUOTES - "beetle's juice" becomes "\\\"beetle\'s juice\\\""
 *                  * MPD_ESCAPE_PREFIX_SPACE  - Adds a space at the params beginning
 *                  * MPD_ESCAPE_SUFFIX_SPACE  - Adds a space at the params ending
 * @return string
 */
function escape_params(array $params, int $flags = MPD_ESCAPE_PREFIX_SPACE): string
{
  $quote = "\"";
  if($flags & MPD_ESCAPE_DOUBLE_QUOTES){
    $quote = "\\\"";
  }

  $prefix = $suffix = "";
  if($flags & MPD_ESCAPE_PREFIX_SPACE){ $prefix = " "; }
  if($flags & MPD_ESCAPE_SUFFIX_SPACE){ $suffix = " "; }

  $parsed = "";
  foreach($params as $param){
    $param = (string)$param;
    // @ToDo make sure this works aka. test this
    if(strlen($param) === 0){ continue; }
    //$param = str_replace("\\", "\\\\", $param);
    $param = escapeshellcmd(escapeshellcmd($param));
    $parsed .= $prefix.$quote.$param.$quote.$suffix;
  }
  return $parsed;
}


/**
 * Function to "convert" int or array to a pos or range argument
 * @param int|array $p
 * @return int|string
 */
function pos_or_range($p)
{
  if(is_array($p)){
    if(!$p){ return ""; }
    return ($p[0] ?? "").":".($p[1] ?? "");
  }elseif($p !== -1){
    return $p;
  }
  return "";
}


/**
 * Function to (not) throw exceptions
 * @param MPDException $e
 * @param int $errormode
 * @return bool
 * @throws MPDException
 */
function error(MPDException $e, int $errormode): bool
{
  if($errormode & MPD_ERRORMODE_EXCEPTION){
    throw $e;
  }
  if($errormode & MPD_ERRORMODE_WARNING){
    trigger_error($e->__toString(), E_USER_WARNING);
  }
  return false;
}



/**
 * Parse an array of lines returned by MPD into a PHP array.
 * @param array $lines Given lines
 * @param int $flag Refer to `cmd()`'s documentation.
 * @return array|true|MPDException
 */
function parse(array $lines, int $flag = MPD_CMD_READ_NORMAL)
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
    if($flag === MPD_CMD_READ_LIST && ($first_key !== NULL && (isset($lines_parsed[$i+1]) && ($lines_parsed[$i+1]["k"] === $first_key)) || !isset($lines_parsed[$i+1]))){
      $b[] = $tmp;
      $tmp = [];

      // If we only parse a list with a single possible key just push its value to the result array.
      // If this is used in a command which returns more than one possible key the result will look a little funky.
      // We can, however, simple blame the user.
    }elseif($flag === MPD_CMD_READ_LIST_SINGLE){
      $b[] = $v;
    }

    // set the first encountered key if there isn't already one
    if(($flag === MPD_CMD_READ_LIST || $flag === MPD_CMD_READ_LIST_SINGLE) && $first_key === NULL){
      $first_key = $k;
    }

  }

  if($flag === MPD_CMD_READ_BOOL){
    return true;
  }

  return ($flag === MPD_CMD_READ_LIST || $flag === MPD_CMD_READ_LIST_SINGLE ? $b : $tmp);
}


/** Function to parse an MPD error string to an array
 * @param string $error The error string. For example "ACK [error@command_listNum] {current_command} message_text"
 * @return MPDException
 */
function parse_error(string $error) : MPDException
{
  if(!str_starts_with($error, "ACK")){
    return new MPDException($error, 1);
  }
  $s = explode(" ", $error, 4);
  $e = explode("@", trim($s[1], "[]"), 2);
  $s[2] = trim($s[2], "{}");

  $message = $s[3];
  $code = (int)$e[0];
  $commandlist_num = (int)$e[1];
  $command = $s[2];

  return new MPDException($message, $code, null, $command, $commandlist_num);
}