<?php declare(strict_types=1);
/*
 * MphpD
 * http://mphpd.org
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

namespace FloFaber\MphpD;

require_once __DIR__ . "/Filter.php";

/**
 * Subclass to interact with stored Playlists.
 * @example MphpD::playlist(string $name) : Playlist
 */
class Playlist
{

  /*
   * ##############################
   *        STORED PLAYLISTS
   * ##############################
   */

  private MphpD $mphpd;
  private string $name;

  /**
   * This class is not intended for direct usage.
   * Use `MphpD::playlist()` instead to retrieve an instance of this class.
   * @param MphpD $mphpd
   * @param string $name
   * @throws MPDException
   */
  public function __construct(MphpD $mphpd, string $name)
  {
    $this->mphpd = $mphpd;
    $this->name = $name;
    if(empty($this->name)){
      throw new MPDException("Playlist name may not be empty", 400);
    }
  }

  /**
   * Function to determine if the specified playlist exists.
   * @return bool `true` if it exists, otherwise `false`.
   */
  public function exists(): bool
  {
    $playlists = $this->mphpd->cmd("listplaylists", [], MPD_CMD_READ_LIST);
    if($playlists === false OR empty($playlists)){
      return false;
    }
    foreach($playlists as $playlist){
      if(isset($playlist["playlist"]) AND $playlist["playlist"] === $this->name){
        return true;
      }
    }
    return false;
  }


  /**
   * Returns a list of all songs in the specified playlist.
   * @param bool $metadata If set to `true` metadata like duration, last-modified,... will be included.
   * @return array|false `array` of associative Arrays containing song information on success or `false` on failure.
   */
  public function get_songs(bool $metadata = false) : array|false
  {
    return $this->mphpd->cmd("listplaylist".($metadata ? "info" : ""), [$this->name], MPD_CMD_READ_LIST);
  }


  /**
   * Loads the specified playlist into the Queue.
   * @param array|null $range Range. If specified only the requested portion of the playlist is loaded. Starts at 0.
   * @param int|null $pos Specifies where the songs will be inserted into the queue.
   *                    Can be relative if prefixed with + or -
   * @return bool `true` on success and `false` on failure.
   */
  public function load(?array $range = null, ?int $pos = null) : bool
  {
    return $this->mphpd->cmd("load", [ $this->name, Utils::pos_or_range($range), $pos ], MPD_CMD_READ_BOOL);
  }


  /**
   * Adds `$uri` to the specified playlist at position `$pos`.
   * @param string $uri Relative file path or other supported URIs.
   * @param int|null $pos Specifies where the songs will be inserted into the playlist.
   *                    Can be relative if prefixed with + or -
   * @return bool `true` on success and `false` on failure.
   */
  public function add(string $uri, ?int $pos = null) : bool
  {
    return $this->mphpd->cmd("playlistadd", [$this->name, $uri, $pos], MPD_CMD_READ_BOOL);
  }


  /**
   * Search for songs using Filter and add them into the Playlist at position `$pos`.
   * @param Filter $filter
   * @param string|null $sort
   * @param array|null $window
   * @param int|null $position
   * @return bool
   * @see DB::search()
   */
  public function add_search(Filter $filter, ?string $sort = null, ?array $window = null, ?int $position = null) : bool
  {
    $name = Utils::escape_params([ $this->name ]);
    return $this->mphpd->cmd("searchaddpl $name $filter", [
        ($sort ? "sort" : null), ($sort ?: null),
        ($window ? "window" : null), ($window ? Utils::pos_or_range($window) : null),
        ($position !== null ? "position" : null), ($position !== null ? $position : null)
      ], MPD_CMD_READ_BOOL);
  }


  /**
   * Removes all songs from the specified playlist.
   * @return bool `true` on success and `false` on failure.
   */
  public function clear() : bool
  {
    return $this->mphpd->cmd("playlistclear", [$this->name], MPD_CMD_READ_BOOL);
  }


  /**
   * Deletes `$songpos` from the specified playlist.
   * @param array|int|null $songpos Position of the song or Range
   * @return bool `true` on success and `false` on failure.
   */
  public function remove_song(array|int|null $songpos = null) : bool
  {
    return $this->mphpd->cmd("playlistdelete", [ $this->name, Utils::pos_or_range($songpos) ], MPD_CMD_READ_BOOL);
  }


  /**
   * Moves the song at position `$from` in the specified playlist to the position `$to`.
   * @param int|array $from Current song position or range
   * @param int $to New song position
   * @return bool `true` on success and `false` on failure.
   */
  public function move_song(int|array $from, int $to) : bool
  {
    return $this->mphpd->cmd("playlistmove", [$this->name, Utils::pos_or_range($from), $to], MPD_CMD_READ_BOOL);
  }


  /**
   * Renames the specified playlist to `$new_name`
   * @param string $new_name New playlist name
   * @return bool `true` on success and `false` on failure.
   */
  public function rename(string $new_name) : bool
  {
    return $this->mphpd->cmd("rename", [$this->name, $new_name], MPD_CMD_READ_BOOL);
  }


  /**
   * Removes the specified playlist from the playlist directory.
   * @return bool `true` on success and `false` on failure.
   */
  public function delete() : bool
  {
    return $this->mphpd->cmd("rm", [$this->name], MPD_CMD_READ_BOOL);
  }


  /**
   * Saves the queue to the specified playlist in the playlist directory
   * @param int $mode Optional. One of the following:
   *
   *                  * MPD_MODE_CREATE: The default. Create a new playlist. Fails if a playlist with name $name already exists.
   *
   *                  * MPD_MODE_APPEND: Append an existing playlist. Fails if a playlist with name $name doesn't already exist.
   *                                     Only supported on MPD v0.24 and newer.
   *
   *                  * MPD_MODE_REPLACE: Replace an existing playlist. Fails if a playlist with name $name doesn't already exist.
   *                                      Only supported on MPD v0.24 and newer.
   * @return bool `true` on success and `false` on failure.
   * @throws MPDException If `$mode` is not supported.
   */
  public function save(int $mode = MPD_MODE_CREATE) : bool
  {

    $m = match ($mode) {
      MPD_MODE_APPEND => "append",
      MPD_MODE_REPLACE => "replace",
      default => "create",
    };

    // ignore the mode parameter on version older than 0.24
    if(!$this->mphpd->version_bte("0.24") && $mode !== MPD_MODE_CREATE){
      throw new MPDException("Mode $m is only supported on MPD v0.24 and newer.");
    }elseif(!$this->mphpd->version_bte("0.24") && $mode == MPD_MODE_CREATE){
      $m = "";
    }

    return $this->mphpd->cmd("save", [$this->name, $m], MPD_CMD_READ_BOOL);
  }


  /**
   * Count the number of songs and their total playtime (seconds) in the playlist.
   * @return bool|array 
   */
  public function length() : bool|array
  {
    return $this->mphpd->cmd("playlistlength", [$this->name]);
  }

}