<?php

namespace FloFaber;

class Status
{
  private MphpD $mphpd;
  public function __construct(MphpD $mphpd)
  {
    $this->mphpd = $mphpd;
  }


  /*
   * ###########################
   *    QUERYING MPD's STATUS
   * ###########################
   */



  /**
   * @return array|false
   * @throws MPDException
   */
  public function clearerror()
  {
    return $this->mphpd->cmd("clearerror");
  }


  /**
   * @return array|false
   * @throws MPDException
   */
  public function currentsong()
  {
    return $this->mphpd->cmd("currentsong");
  }


  /**
   * Waits until there is a noteworthy change in one or more of MPD’s subsystems.
   * @param string $subsystem
   * @param int $timeout Specifies how long to wait for MPD to return an answer.
   * @return array|false Returns an array of changed subsystems or false on timeout.
   * @throws MPDException
   */
  public function idle(string $subsystem = "", int $timeout = 60)
  {
    stream_set_timeout($this->mphpd->get_socket(), $timeout);
    $subsystems = $this->mphpd->cmd("idle", [$subsystem], MPD_CMD_READ_LIST_SINGLE);


    // if the stream timed out we need to send "noidle". Otherwise, MPD won't know that we don't want to wait anymore.
    // Alternatively reconnecting would also work.
    $metadata = stream_get_meta_data($this->mphpd->get_socket());
    if($metadata["timed_out"]){
      $this->mphpd->cmd("noidle");
    }

    return $subsystems;
  }


  /**
   * Returns the value of the specified key(s) from MPD's status.
   * @param array $items Optional. Array containing the wanted key(s) like `status`, `songid`,...
   *
   *                     If only one item is given only it's value will be returned instead of an associative array.
   *
   *                     If the given item(s) do not exist `null` will be set as their value.
   *
   *                     If omitted, an associative array containing all status information will be returned.
   *
   * @return array|false|int|null Returns
   *                     `false` on error
   *
   *                     `string`, `int` or `null` if $items contains only one item. If it does not exist `null` will be returned instead.
   *
   *                     Otherwise, an associative array containing all available (or specified) keys.
   * @throws MPDException
   */
  public function get(array $items = [])
  {
    $status = $this->mphpd->cmd("status");

    // if status has failed return false
    if($status === false){ return false; }

    // if no items are given return the whole status
    if(!$items){ return $status; }

    // if items contain a single item return it's value or null if it doesn't exist
    if(count($items) === 1){
      return $status[$items[0]] ?? null;
    }

    // otherwise return only the requested items
    $tmp = [];
    foreach($items as $item){
      $tmp[$item] = $status[$item] ?? null;
    }

    return $tmp;
  }


  /**
   * Returns the value of the specified key from MPD's stats.
   * @param array $items Optional. Array containing the wanted stat(s). Example: `[ "artists", "uptime", "playtime" ]`
   *
   *                     If only one item is given only it's value will be returned instead of an associative array.
   *
   *                    If the given item(s) do not exist `null` will be set as their value.
   *
   *                    If omitted, an associative array containing all stats will be returned.
   *
   * @return array|false|int|null Returns
   *                     `false` on error
   *
   *                     `string`, `int` or `null` if $items contains only one item. If it does not exist `null` will be returned instead.
   *
   *                     Otherwise, an associative array containing all available (or specified) stats.
   * @throws MPDException
   */
  public function stats(array $items = [])
  {
    $stats = $this->mphpd->cmd("stats");

    // if stats failed return false immediately
    if($stats === false){ return false; }

    // if no items are given return all stats
    if(!$items){ return $stats; }

    // if items contain a single item return it's value or null if it doesn't exist
    if(count($items) === 1){
      return $stats[$items[0]] ?? null;
    }

    // otherwise return only the requested items
    $tmp = [];
    foreach($items as $item){
      $tmp[$item] = $stats[$item] ?? null;
    }

    return $tmp;
  }

}