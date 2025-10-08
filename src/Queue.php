<?php declare(strict_types=1);
/*
 * MphpD
 * http://mphpd.org
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

namespace FloFaber\MphpD;

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Subclass to control the Queue.
 * @example MphpD::queue() : Queue
 */
class Queue
{
  private MphpD $mphpd;

  /**
   * This class is not intended for direct usage.
   * Use `MphpD::queue()` instead to retrieve an instance of this class.
   * @param MphpD $mphpd
   */
  public function __construct(MphpD $mphpd)
  {
    $this->mphpd = $mphpd;
  }


  /**
   * Adds the file `$uri` to the queue (directories add recursively). `$uri` can also be a single file.
   * @param string $uri Can be a single file or folder.
   *                    If connected via Unix socket you may add arbitrary local files (absolute paths)
   * @param int|string|null $pos If set the song is inserted at the specified position.
   *                 If the parameter starts with + or -, then it is relative to the current song.
   *                 e.g. +0 inserts right after the current song and -0 inserts right before the current song (i.e. zero songs between the current song and the newly added song).
   * @return bool `true` on success and `false` on failure.
   */
  public function add(string $uri, int|string|null $pos = null) : bool
  {
    return $this->mphpd->cmd("add", [ $uri, ($pos !== null ? $pos : null) ], MPD_CMD_READ_BOOL);
  }


  /**
   * Adds a song to the playlist (non-recursive) and returns the song id.
   * @param string $uri Is always a single file or URL
   * @param int|string|null $pos If set the song is inserted at the specified position.
   *                 If the parameter starts with + or -, then it is relative to the current song.
   *                 e.g. +0 inserts right after the current song and -0 inserts right before the current song (i.e. zero songs between the current song and the newly added song).
   * @return int|false The song ID on success or `false` on failure.
   */
  public function add_id(string $uri, int|string|null $pos = null): false|int
  {
    $add_id = $this->mphpd->cmd("addid", [ $uri, ($pos !== null ? $pos : null) ]);
    if(!$add_id){ return false; }
    return $add_id["id"] ?? false;
  }


  /**
   * Same as `Queue::search()` but adds the songs into the Queue at position `$pos`.
   * @param Filter $filter
   * @param string|null $sort Optional
   * @param array|null $window Optional
   * @param int|null $position Songs will be added to this position in the Queue.
   * @param bool $case_sensitive Specifies if the search should be performed case-sensitive or not.
   * @return bool `true` on success and `false` on failure.
   * @deprecated DB::search_add
   */
  public function add_search(Filter $filter, ?string $sort = null, ?array $window = null, ?int $position = null, bool $case_sensitive = false) : bool
  {
    return $this->mphpd->db()->search_add($filter, $sort, $window, $case_sensitive, $position);
  }


  /**
   * Kept for backward-compatibility. Consider using Queue::add_search instead.
   * Same as `Queue::find()` but adds the songs into the Queue at position `$pos`.
   * @param Filter $filter
   * @param string|null $sort Optional
   * @param array|null $window Optional
   * @param int|null $position Optional. If specified the matched songs will be added to this position in the Queue.
   * @return bool `true` on success and `false` on failure.
   * @deprecated DB::search_add
   */
  public function add_find(Filter $filter, ?string $sort = null, ?array $window = null, ?int $position = null): bool
  {
    return $this->mphpd->db()->search_add($filter, $sort, $window, true, $position);
  }


  /**
   * Clears the queue
   * @return bool `true` on success and `false` on failure.
   */
  public function clear() : bool
  {
    return $this->mphpd->cmd("clear", [], MPD_CMD_READ_BOOL);
  }


  /**
   * Deletes a song or a range of songs from the queue
   * @param int|array $p The song position or Range
   * @return bool `true` on success and `false` on failure.
   */
  public function delete(int|array $p) : bool
  {
    return $this->mphpd->cmd("delete", [ Utils::pos_or_range($p) ], MPD_CMD_READ_BOOL);
  }


  /**
   * Deletes the song with ID `$songid` from the Queue
   * @param int $songid The song ID
   * @return bool `true` on success and `false` on failure.
   */
  public function delete_id(int $songid) : bool
  {
    return $this->mphpd->cmd("deleteid", [$songid], MPD_CMD_READ_BOOL);
  }


  /**
   * Moves the song at `$from` to `$to` in the queue
   * @param int|array $from Song position or Range
   * @param int|string $to If starting with + or -, then it is relative to the current song
   *                   e.g. +0 moves to right after the current song and -0 moves to right before the current song
   *                   (i.e. zero songs between the current song and the moved range).
   * @return bool `true` on success and `false` on failure.
   */
  public function move(int|array $from, int|string $to) : bool
  {
    if(!is_numeric($to)){
      $this->mphpd->set_error(new MPDException("\$to is not numeric.", 400));
    }

    return $this->mphpd->cmd("move", [ Utils::pos_or_range($from), $to ], MPD_CMD_READ_BOOL);
  }


  /**
   * Moves the song with `$from` (songid) to `$to` (playlist index) in the queue.
   * @param int $from Song ID
   * @param int|string $to Playlist Index. If starting with + or -, then it is relative to the current song
   *                   e.g. +0 moves to right after the current song and -0 moves to right before the current song
   *                   (i.e. zero songs between the current song and the moved song).
   * @return bool `true` on success and `false` on failure.
   */
  public function move_id(int $from, int|string $to) : bool
  {
    if(!is_numeric($to)){
      $this->mphpd->set_error(new MPDException("\$to is not numeric.", 400));
    }

    return $this->mphpd->cmd("moveid", [$from, $to], MPD_CMD_READ_BOOL);
  }


  /**
   * Kept for backward-compatibility. Consider using Queue::search instead.
   * Same as Queue::search but case-sensitive
   * @param Filter $filter
   * @param string|null $sort
   * @param array|null $window
   * @return array|false `array` on success or `false` on failure.
   * @deprecated As of v2.0.0
   */
  public function find(Filter $filter, ?string $sort = null, ?array $window = null): false|array
  {
    return $this->search($filter, $sort, $window, true);
  }


  /**
   * Returns an associative arrays containing information about the song with ID `$songid`.
   * @param int $songid Song ID
   * @return array|false Associative `array` containing song information or `false` on failure.
   */
  public function get_id(int $songid): false|array
  {
    return $this->mphpd->cmd("playlistid", [ $songid ]);
  }


  /**
   * Returns information about all or specific songs in the Queue.
   * @param $pos int|array|null Optional. Song Position or Range.
   *                     If omitted returns an array of associative arrays containing information about all songs in the Queue.
   *                     If specified returns an associative array containing the given songs information only.
   * @param bool $metadata If `false` only file-URIs will be returned
   * @return array|false `array` on success or `false` on failure. An empty `array` is returned if the queue is empty.
   */
  public function get(int|array|null $pos = null, bool $metadata = true): false|array
  {
    $m = MPD_CMD_READ_LIST;
    if(!is_array($pos)){
      $m = MPD_CMD_READ_NORMAL;
    }
    if($pos === null){
      $m = MPD_CMD_READ_LIST;
    }
    
    $queue = $this->mphpd->cmd("playlistinfo", [ Utils::pos_or_range($pos) ], $m);

    if($metadata){
      return $queue;
    }

    // remove all metadata but "file"
    $queue_ = [];
    foreach ($queue as $item) {
      $queue_[] = [ "file" => $item["file"]];
    }
    return $queue_;
  }


  /**
   * Search the queue for matching songs.
   * @param Filter $filter The Filter.
   * @param string|null $sort If specified the results are sorted by the specified tag.
   * @param array|null $window If specified returns only the given portion.
   * @param bool $case_sensitive Specified if the search should be performed case-sensitive
   * @return array|false `array` on success or `false` on failure.
   */
  public function search(Filter $filter, ?string $sort = null, ?array $window = null, bool $case_sensitive = false): false|array
  {

    if($case_sensitive){
      $cmd = "playlistfind";
    }else{
      $cmd = "playlistsearch";
    }

    return $this->mphpd->cmd("$cmd $filter", [
      ($sort ? "sort" : null), ($sort ?: null),
      ($window ? "window" : null), ($window ? Utils::pos_or_range($window) : null)
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Returns an array of changed songs currently in the playlist since `$version`.
   * @param int $version The current version can be retrieved with `MphpD::status([ "playlist" ])`.
   * @param int|array|null $range Position of song or Range
   * @param bool $metadata If set to true the metadata will be included.
   *
   *                       If set to false only the position and ID of the changed songs will be returned.
   * @return array|false `array` on success or `false` on failure.
   */
  public function changes(int $version, int|array|null $range = null, bool $metadata = false): false|array
  {
    $cmd = "plchangesposid";
    if($metadata === true){
      $cmd = "plchanges";
    }
    return $this->mphpd->cmd($cmd, [ $version, Utils::pos_or_range($range) ], MPD_CMD_READ_LIST);
  }


  /**
   * Sets the priority of given songs to `$priority`.
   * This only has effect when the `random`-mode is enabled.
   * A higher priority means that it will be played first when `random` is enabled.
   * @param int $priority Priority 0-255.
   * @param array|int|null $range Position of song or Range
   * @return bool `true` on success or `false` on failure.
   */
  public function prio(int $priority, array|int|null $range = null) : bool
  {
    return $this->mphpd->cmd("prio", [ $priority, Utils::pos_or_range($range) ], MPD_CMD_READ_BOOL);
  }


  /**
   * Sets the priority of Song ID `$id` to $priority.
   * This only has effect when the `random`-mode is enabled.
   * A higher priority means that it will be played first when `random` is enabled.
   * @param int $priority Priority 0-255.
   * @param int $id Song ID.
   * @return bool `true` on success or `false` on failure.
   */
  public function prio_id(int $priority, int $id) : bool
  {
    return $this->mphpd->cmd("prioid", [ $priority, $id ], MPD_CMD_READ_BOOL);
  }


  /**
   * Set's the portion of the song that should be played.
   * You can't edit the currently playing song!
   * @param int $songid Song ID.
   * @param array $range Range. Start and End are offsets in seconds. If omitted the "play-range" will be removed from the song.
   * @return bool `true` on success or `false` on failure.
   */
  public function range_id(int $songid, array $range = []) : bool
  {

    // we misuse the pos_or_range here as it works the same here
    $pos = Utils::pos_or_range($range);

    // sending just ":" means "remove the range, play everything"
    if(!$range){
      $pos = ":";
    }
    return $this->mphpd->cmd("rangeid", [ $songid, $pos ], MPD_CMD_READ_BOOL);
  }


  /**
   * Shuffle the Queue.
   * @param array $range If specified only this portion will be shuffled.
   * @return bool `true` on success or `false` on failure.
   */
  public function shuffle(array $range = []) : bool
  {
    return $this->mphpd->cmd("shuffle", [ Utils::pos_or_range($range) ], MPD_CMD_READ_BOOL);
  }


  /**
   * Swap two songs in Queue. By Position.
   * @param int $songpos_1
   * @param int $songpos_2
   * @return bool `true` on success or `false` on failure.
   */
  public function swap(int $songpos_1, int $songpos_2) : bool
  {
    return $this->mphpd->cmd("swap", [$songpos_1, $songpos_2], MPD_CMD_READ_BOOL);
  }


  /**
   * Swap two songs in Queue. By ID.
   * @param int $songid_1 First song ID
   * @param int $songid_2 Second song ID
   * @return bool `true` on success or `false` on failure.
   */
  public function swap_id(int $songid_1, int $songid_2) : bool
  {
    return $this->mphpd->cmd("swapid", [$songid_1, $songid_2], MPD_CMD_READ_BOOL);
  }


  /**
   * Adds a tag to the specified song. Editing song tags is only possible for remote songs. Also this change is volatile.
   * @param int $songid Song ID
   * @param string $tag Tag name
   * @param string $value Tag value
   * @return bool `true` on success or `false` on failure.
   */
  public function add_tag_id(int $songid, string $tag, string $value) : bool
  {
    return $this->mphpd->cmd("addtagid", [$songid, $tag, $value], MPD_CMD_READ_BOOL);
  }


  /**
   * Removes a tag from the specified song
   * @param int $songid Song ID
   * @param string $tag Tag name
   * @return bool `true` on success or `false` on failure.
   */
  public function clear_tag_id(int $songid, string $tag) : bool
  {
    return $this->mphpd->cmd("cleartagid", [$songid, $tag], MPD_CMD_READ_BOOL);
  }


}
