<?php

namespace FloFaber;

class Queue
{
  private MphpD $mphpd;
  public function __construct(MphpD $mphpd)
  {
    $this->mphpd = $mphpd;
  }


  /**
   * Adds the file $uri to the queue (directories add recursively). $uri can also be a single file.
   * @param string $uri Can be a single file or folder.
   *
   *                    If connected via Unix socket you may add arbitrary local files (absolute paths)
   * @param int|string $pos If set the song is inserted at the specified position.
   *                 If the parameter starts with + or -, then it is relative to the current song.
   *                 e.g. +0 inserts right after the current song and -0 inserts right before the current song (i.e. zero songs between the current song and the newly added song).
   * @return bool Returns `true` on success and `false` on failure.
   * @throws MPDException
   */
  public function add(string $uri, $pos = -1) : bool
  {
    return $this->mphpd->cmd("add", [ $uri, ($pos !== -1 ? $pos : "") ]) !== false;
  }


  /**
   * Adds a song to the playlist (non-recursive) and returns the song id.
   * @param string $uri Is always a single file or URL
   * @param int|string $pos If set the song is inserted at the specified position.
   *                 If the parameter starts with + or -, then it is relative to the current song.
   *                 e.g. +0 inserts right after the current song and -0 inserts right before the current song (i.e. zero songs between the current song and the newly added song).
   * @return int|false Returns the song ID on success or false on failure.
   * @throws MPDException
   */
  public function addid(string $uri, $pos = -1) : array
  {
    $addid = $this->mphpd->cmd("addid", [ $uri, ($pos !== -1 ? $pos : "") ]);
    if(!$addid){ return false; }
    return $addid["id"] ?? false;
  }


  /**
   * Clears the queue
   * @return bool Returns true on success and false on failure.
   * @throws MPDException
   */
  public function clear() : bool
  {
    return $this->mphpd->cmd("clear") !== false;
  }


  /**
   * Deletes a song or a range of songs from the queue
   * @param int|array $p The song position or Range
   * @return bool
   * @throws MPDException
   */
  public function delete($p) : bool
  {
    return $this->mphpd->cmd("delete", [ pos_or_range($p) ]) !== false;
  }


  /**
   * Deletes the song with ID $songid from the Queue
   * @param int $songid
   * @return bool
   * @throws MPDException
   */
  public function deleteid(int $songid) : bool
  {
    return $this->mphpd->cmd("deleteid", [$songid]) !== false;
  }


  /**
   * Moves the song at $from to $to in the queue
   * @param int|array $from Song position or Range
   * @param string $to If starting with + or -, then it is relative to the current song
   *                   e.g. +0 moves to right after the current song and -0 moves to right before the current song
   *                   (i.e. zero songs between the current song and the moved range).
   * @return bool
   * @throws MPDException
   */
  public function move($from, string $to) : bool
  {
    if(!is_numeric($to))
      $this->mphpd->setError(new MPDException("\$to is not numeric.", 400));

    return $this->mphpd->cmd("move", [pos_or_range($from), $to]) !== false;
  }


  /**
   * Moves the song with $from (songid) to $to (playlist index) in the queue
   * @param int $from
   * @param string $to If starting with + or -, then it is relative to the current song
   *                   e.g. +0 moves to right after the current song and -0 moves to right before the current song
   *                   (i.e. zero songs between the current song and the moved song).
   * @return bool
   * @throws MPDException
   */
  public function moveid(int $from, string $to) : bool
  {
    if(!is_numeric($to))
      $this->mphpd->setError(new MPDException("\$to is not numeric.", 400));
    return $this->mphpd->cmd("moveid", [$from, $to]) !== false;
  }


  public function find(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("playlistfind", [
      $filter,
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Returns an associative arrays containing information about the song with ID $songid.
   * @param int $songid
   * @return array|bool Associative array containing song information or false on failure.
   * @throws MPDException
   */
  public function getID(int $songid)
  {
    return $this->mphpd->cmd("playlistid", [ $songid ], MPD_CMD_READ_NORMAL);
  }


  /**
   * If $p is omitted returns an array of associative arrays containing information about songs in the Queue.
   * If $p is specified returns an associative array containing the given songs information only.
   * @param $p int|array Optional. Song Position or Range.
   *
   *                     If omitted all songs in the queue will be returned.
   * @return array|false Array on success. False on failure.
   * @throws MPDException
   */
  public function get($p = -1) : array
  {
    $m = MPD_CMD_READ_LIST;
    if(!is_array($p)){
      $m = MPD_CMD_READ_NORMAL;
    }
    return $this->mphpd->cmd("playlistinfo", [ pos_or_range($p) ], $m);
  }


  /**
   * Search the queue for matching songs.
   * @param Filter $filter The Filter.
   * @param string $sort If specified the results are sorted by the specified tag.
   * @param array $window If specified returns only the given portion.
   * @return array|false Returns array on success and false on failure.
   * @throws MPDException
   */
  public function search(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("playlistsearch $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Returns an array of changed songs currently in the playlist since $version.
   * @param int $version The current version can be retrieved with MphpD->status()->get("playlist").
   * @param int|array $range Position of song or Range
   * @param bool $metadata If set to true the metadata will be included.
   *
   *                       If set to false only the position and ID of the changed songs will be returned.
   * @return array|false Returns array on success and false on failure.
   * @throws MPDException
   */
  public function changes(int $version, $range = -1, bool $metadata = false)
  {
    $cmd = "plchanges";
    if($metadata === true){
      $cmd = "plchangesposid";
    }
    return $this->mphpd->cmd($cmd, [$version, pos_or_range($range) ], MPD_CMD_READ_LIST);
  }


  /**
   * Sets the priority of given songs to $priority.
   * This only has effect when the `random`-mode is enabled.
   * A higher priority means that it will be played first when `random` is enabled.
   * @param int $priority Priority. 0-255.
   * @param int|array $range Position of song or Range
   * @return bool
   * @throws MPDException
   */
  public function prio(int $priority, $range = -1) : bool
  {
    return $this->mphpd->cmd("prio", [ $priority, pos_or_range($range) ]) !== false;
  }


  /**
   * Sets the priority of Song ID $id to $priority.
   * This only has effect when the `random`-mode is enabled.
   * A higher priority means that it will be played first when `random` is enabled.
   * @param int $priority
   * @param int $id
   * @return bool
   * @throws MPDException
   */
  public function prioid(int $priority, int $id) : bool
  {
    return $this->mphpd->cmd("prioid", [ $priority, $id ]) !== false;
  }


  /**
   * Set's the portion of the song that should be played.
   * You can't edit the currently playing song!
   * @param int $songid
   * @param array $range Range. Start and End are offsets in seconds. If omitted the "play-range" will be removed from the song.
   * @return bool
   * @throws MPDException
   */
  public function rangeid(int $songid, array $range = []) : bool
  {
    return $this->mphpd->cmd("rangeid", [ $songid, pos_or_range($range) ]) !== false;
  }


  /**
   * Shuffle the Queue.
   * @param array $range If specified only this portion will be shuffled.
   * @return bool
   * @throws MPDException
   */
  public function shuffle(array $range = []) : bool
  {
    return $this->mphpd->cmd("shuffle", [pos_or_range($range)]) !== false;
  }


  /**
   * Swap two songs in Queue. By Position.
   * @param int $songpos_1
   * @param int $songpos_2
   * @return bool
   * @throws MPDException
   */
  public function swap(int $songpos_1, int $songpos_2) : bool
  {
    return $this->mphpd->cmd("swap", [$songpos_1, $songpos_2]) !== false;
  }


  /**
   * Swap two songs in Queue. By ID.
   * @param int $songid_1
   * @param int $songid_2
   * @return bool
   * @throws MPDException
   */
  public function swapid(int $songid_1, int $songid_2) : bool
  {
    return $this->mphpd->cmd("swapid", [$songid_1, $songid_2]) !== false;
  }


  /**
   * Adds a tag to the specified song
   * @param int $songid
   * @param string $tag Tag name
   * @param string $value Tag value
   * @return bool
   * @throws MPDException
   */
  public function addtagid(int $songid, string $tag, string $value) : bool
  {
    return $this->mphpd->cmd("addtagid", [$songid, $tag, $value]) !== false;
  }


  /**
   * Removes a tag from the specified song
   * @param int $songid
   * @param string $tag Tag name
   * @return bool
   * @throws MPDException
   */
  public function cleartagid(int $songid, string $tag) : bool
  {
    return $this->mphpd->cmd("cleartagid", [$songid, $tag]) !== false;
  }


}