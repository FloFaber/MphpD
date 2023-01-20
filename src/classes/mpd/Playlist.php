<?php
/*
 * MphpD
 * http://mphpd.org
 *
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

namespace FloFaber;

/**
 * Subclass to interact with stored Playlists.
 * @title Playlists
 * @usage MphpD::playlist(string $name) : Playlist
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
   * @return bool True if it exists. False if it doesn't.
   */
  public function exists(): bool
  {
    try{
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
    }catch (MPDException $e){
      return false;
    }
  }


  /**
   * Returns a list of all songs in the specified playlist.
   * @param bool $metadata If set to true metadata like duration, last-modified,... will be included.
   * @return array|false On success returns an Array of associative Arrays containing song information. False on failure.
   * @throws MPDException
   */
  public function get_songs(bool $metadata = false) : array
  {
    return $this->mphpd->cmd("listplaylist".($metadata ? "info" : ""), [$this->name], MPD_CMD_READ_LIST);
  }


  /**
   * Loads the specified playlist into the Queue.
   * @param array $range Range. If specified only the requested portion of the playlist is loaded. Starts at 0.
   * @param int|string $pos The $pos parameter specifies where the songs will be inserted into the queue.
   *
   *                    Can be relative if prefixed with + or -
   * @return bool
   * @throws MPDException
   */
  public function load(array $range = [], $pos = "") : bool
  {
    return $this->mphpd->cmd("load", [ $this->name, pos_or_range($range), $pos ], MPD_CMD_READ_BOOL);
  }


  /**
   * Adds $uri to the specified playlist at position $pos.
   * @param string $uri Relative file path or other supported URIs.
   * @param int|string $pos Specifies where the songs will be inserted into the playlist.
   *                    Can be relative if prefixed with + or -
   * @return bool Returns true on success and false on failure.
   * @throws MPDException
   */
  public function add(string $uri, $pos = "") : bool
  {
    return $this->mphpd->cmd("playlistadd", [$this->name, $uri, $pos], MPD_CMD_READ_BOOL);
  }


  /**
   * Search for songs using Filter and add them into the Playlist at position $pos.
   * @see DB::search()
   * @param Filter $filter
   * @param string $sort
   * @param array $window
   * @param int $position
   * @return bool
   * @throws MPDException
   */
  public function add_search(Filter $filter, string $sort = "", array $window = [], int $position = -1) : bool
  {
    $name = escape_params([ $this->name ]);
    return $this->mphpd->cmd("searchaddpl $name $filter", [
        ($sort ? "sort" : ""), ($sort ?: ""),
        ($window ? "window" : ""), ($window ? pos_or_range($window) : ""),
        ($position !== -1 ? "position" : ""), ($position !== -1 ? $position : "")
      ], MPD_CMD_READ_BOOL);
  }


  /**
   * Removes all songs from the specified playlist.
   * @return bool Returns true on success and false on failure.
   * @throws MPDException
   */
  public function clear() : bool
  {
    return $this->mphpd->cmd("playlistclear", [$this->name], MPD_CMD_READ_BOOL);
  }


  /**
   * Deletes $songpos from the specified playlist.
   * @param int|array $songpos Position of the song or Range
   * @return bool
   * @throws MPDException
   */
  public function remove_song($songpos = -1) : bool
  {
    return $this->mphpd->cmd("playlistdelete", [ $this->name, pos_or_range($songpos) ], MPD_CMD_READ_BOOL);
  }


  /**
   * Moves the song at position $from in the specified playlist to the position $to.
   * @param int $from
   * @param int $to
   * @return bool
   * @throws MPDException
   */
  public function move_song(int $from, int $to) : bool
  {
    return $this->mphpd->cmd("playlistmove", [$this->name, $from, $to], MPD_CMD_READ_BOOL);
  }


  /**
   * Renames the specified playlist to $new_name
   * @param string $new_name New playlist name
   * @return bool
   * @throws MPDException
   */
  public function rename(string $new_name) : array
  {
    return $this->mphpd->cmd("rename", [$this->name, $new_name], MPD_CMD_READ_BOOL);
  }


  /**
   * Removes the specified playlist from the playlist directory.
   * @return bool
   * @throws MPDException
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
   * @return bool True on success. False on failure.
   * @throws MPDException
   */
  public function save(int $mode = MPD_MODE_CREATE) : bool
  {

    switch ($mode){
      case MPD_MODE_APPEND:
        $m = "append"; break;
      case MPD_MODE_REPLACE:
        $m = "replace"; break;
      default:
        $m = "create"; break;
    }

    // ignore the mode parameter on version older than 0.24
    if(!$this->mphpd->version_bte("0.24")){
      $m = "";
    }

    return $this->mphpd->cmd("save", [$this->name, $m], MPD_CMD_READ_BOOL);
  }

}