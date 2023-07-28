<?php
/*
 * MphpD
 * http://mphpd.org
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

namespace FloFaber\MphpD;

require_once __DIR__ . "/Utils.php";
require_once __DIR__ . "/Filter.php";


/**
 * This subclass is used to interact with and retrieve information from MPD's database.
 * @example MphpD::db() : DB
 */
class DB
{

  private MphpD $mphpd;

  /**
   * This class is not intended for direct usage.
   * Use `MphpD::db()` instead to retrieve an instance of this class.
   * @param MphpD $mphpd
   */
  public function __construct(MphpD $mphpd)
  {
    $this->mphpd = $mphpd;
  }


  /**
   * Returns the albumart (binary!) for given song.
   * @param string $songuri
   * @return false|string binary `string` on success or `false` on failure.
   */
  public function albumart(string $songuri)
  {
    $offset = 0;
    $binary_data = "";
    do{

      $aa = $this->mphpd->cmd("albumart", [$songuri, $offset]);
      if($aa === false){ return false; }

      $binary_size = $aa["size"];

      $offset = $offset + $this->mphpd->get_binarylimit();
      $binary_data .= $aa["binary_data"];

    }while($offset < $binary_size);

    return $binary_data;
  }


  /**
   * Counts the number of songs and their playtime matching the specified Filter.
   * @param Filter $filter
   * @param string $group A tag name like `artist` by which the results will be grouped.
   *
   *                      If omitted returns an associative array containing a "songs" and "playtime" key.
   *
   *                      If specified an array of associative array will be returned.
   * @return array|false `array` on success or `false` on failure.
   */
  public function count(Filter $filter, string $group = "")
  {
    $m = MPD_CMD_READ_NORMAL;
    if(!empty($group)){
      $m = MPD_CMD_READ_GROUP;
    }
    return $this->mphpd->cmd("count $filter", [
      ($group ? "group" : ""), ($group ?: "")
    ], $m);
  }


  /**
   * Calculate the song's fingerprint
   * @param string $uri URI to the file.
   * @return string|false fingerprint on success or `false` on failure.
   */
  public function fingerprint(string $uri)
  {
    $fp = $this->mphpd->cmd("getfingerprint", [$uri]);
    if($fp === false){ return false; }
    return $fp["chromaprint"] ?? false;
  }


  /**
   * Case-sensitive search for songs matching Filter and return an array of associative array of found songs.
   * @param Filter $filter
   * @param string $sort Tag name to sort by. Like artist. If prefixed with `-` it will be sorted descending.
   *
   *                     If omitted the order is undefined.
   * @param array $window Retrieve only a given portion
   * @return array|false `array` on success and `false` on failure.
   */
  public function find(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("find $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? Utils::pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Lists unique tags values of the specified type. `$type` can be any tag supported by MPD.
   *
   * @param string $type Any tag supported by MPD. Like artist or album.
   * @param Filter|null $filter
   * @param string $group Tag name by which the result gets grouped. Like artist or album.
   *
   *                      If omitted returns an array of unique tag values of the specified type.
   *
   *                      If specified returns an array of associative arrays containing the grouped result.
   * @return array|false `array` on success or `false` on failure.
   */
  public function list(string $type, Filter $filter = null, string $group = "")
  {
    $m = MPD_CMD_READ_LIST_SINGLE;
    if(!empty($group)){
      $m = MPD_CMD_READ_GROUP;
    }

    $type = Utils::escape_params([ $type ]);
    return $this->mphpd->cmd("list $type $filter", [
      ($group ? "group" : ""), ($group ?: "")
    ], $m);
  }


  /**
   * List files,directories and playlists in `$uri`
   * @param string $uri Directory URI.
   * @param bool $metadata Specifies if additional information should be included.
   * @param bool $recursive Specified if files and directories should be listed recursively.
   * @return array|false `array` containing the keys `files`, `directories` and `playlists` on success or `false` on failure.
   */
  public function ls(string $uri, bool $metadata = false, bool $recursive = false)
  {

    if($metadata && $recursive){
      $items = $this->mphpd->cmd("listallinfo", [$uri], MPD_CMD_READ_LIST, [ "file", "directory", "playlist" ]);
    }elseif(!$metadata && $recursive){
      $items = $this->mphpd->cmd("listall", [$uri], MPD_CMD_READ_LIST, [ "file", "directory", "playlist" ]);
    }elseif($metadata && !$recursive){
      $items = $this->mphpd->cmd("lsinfo", [$uri], MPD_CMD_READ_LIST, [ "file", "directory", "playlist" ]);
    }elseif(!$metadata && !$recursive){
      $items = $this->mphpd->cmd("listfiles", [$uri], MPD_CMD_READ_LIST, [ "file", "directory", "playlist" ]);
    }else{ $items = false; }

    if($items === false){ return false; }

    $files = [];
    $directories = [];
    $playlists = [];

    // split items into multiple arrays for more usability
    foreach($items as $item){
      if(isset($item["file"])){
        $item["name"] = $item["file"]; unset($item["file"]);
        $files[] = $item;
      }elseif(isset($item["directory"])){
        $item["name"] = $item["directory"]; unset($item["directory"]);
        $directories[] = $item;
      }elseif(isset($item["playlist"])){
        $item["name"] = $item["playlist"]; unset($item["playlist"]);
        $playlists[] = $item;
      }
    }

    return [
      "files" => $files,
      "directories" => $directories,
      "playlists" => $playlists
    ];

  }


  /**
   * Read "comments" from the specified file.
   * The meaning of these "comments" depend on the codec. For an OGG file this lists the vorbis commands.
   * @param string $uri Song URI.
   * @return array|false `array` on success or `false` on failure.
   */
  public function read_comments(string $uri)
  {
    return $this->mphpd->cmd("readcomments", [$uri], MPD_CMD_READ_LIST);
  }


  /**
   * Returns a picture of `$uri` by reading embedded pictures from binary tags.
   * @param string $uri Song URI.
   * @return false|string binary-data `string` on success and `false` on failure.
   */
  public function read_picture(string $uri)
  {
    $offset = 0;
    $binary_data = "";
    do{

      $aa = $this->mphpd->cmd("readpicture", [$uri, $offset]);
      if($aa === false){ return false; }

      $binary_size = $aa["size"];

      $offset = $offset + $this->mphpd->get_binarylimit();
      $binary_data .= $aa["binary_data"];

    }while($offset < $binary_size);

    return $binary_data;
  }


  /**
   * Case-INsensitive search for matching songs and returns an array of associative arrays containing song information.
   * @see DB::find()
   * @param Filter $filter
   * @param string $sort
   * @param array $window
   * @return array|false `array` on success or `false` on failure.
   */
  public function search(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("search $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? Utils::pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Update the Database and return the Job-ID.
   * @param string $uri Optional. Only update the given path. Omit or specify an empty string to update everything.
   * @param bool $rescan If set to `true` also rescan unmodified files.
   * @param bool $force If set to `false` and an update Job is already running, just return its ID.
   *
   *                    If true and an update Job is already running it starts another one and returns the ID of the new Job.
   * @return int|false Job-ID on success or `false` on failure.
   */
  public function update(string $uri = "", bool $rescan = false, bool $force = false)
  {

    $cmd = "update";
    if($rescan === true){
      $cmd = "rescan";
    }

    // if a job is already running but $force is false return the current Job ID
    $job = $this->mphpd->status([ "updating_db" ]);
    if($job !== false AND $job !== null AND $force === false){
      return $job;
    }

    // otherwise update the DB and return the new Job ID
    $update = $this->mphpd->cmd($cmd, [$uri]);
    if($update === false){ return false; }
    if(isset($update["updating_db"]) AND is_numeric($update["updating_db"])){
      return $update["updating_db"];
    }
    return false;

  }


}
