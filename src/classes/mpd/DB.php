<?php
/*
 * MphpD
 * http://mphpd.org
 *
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

namespace FloFaber;

require_once __DIR__ . "/../Filter.php";

/**
 * This subclass is used to interact with and retrieve information from MPD's database.
 * @title The Database
 * @usage MphpD::DB() : DB
 */
class DB
{

  private MphpD $mphpd;

  public function __construct(MphpD $mphpd)
  {
    $this->mphpd = $mphpd;
  }


  /**
   * Returns the albumart (binary!) for given song.
   * @param string $songuri
   * @return false|string Returns binary data on success or false on failure.
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
   * If $group is omitted returns an associative array containing a "songs" and "playtime" key.
   * If $group is specified an array of associative array will be returned.
   * @param Filter $filter
   * @param string $group A tag name like `artist`. If specified the results will be grouped by this tag.
   * @return array|false
   */
  public function count(Filter $filter, string $group = "")
  {
    $m = MPD_CMD_READ_NORMAL;
    if(!empty($group)){
      $m = MPD_CMD_READ_LIST;
    }
    return $this->mphpd->cmd("count $filter", [
      ($group ? "group" : ""), ($group ?: "")
    ], $m);
  }


  /**
   * Calculate the song's fingerprint
   * @param string $uri
   * @return string|false Returns the fingerprint on success or false on failure.
   */
  public function fingerprint(string $uri)
  {
    $fp = $this->mphpd->cmd("getfingerprint", [$uri]);
    if($fp === false){ return false; }
    return $fp["chromaprint"] ?? false;
  }


  /**
   * Search for songs matching Filter and return an array of associative array of found songs.
   * Case-sensitive!
   * @param Filter $filter
   * @param string $sort Tag name to sort by. Like artist. If prefixed with `-` it will be sorted descending.
   *
   *                     If omitted the order is undefined.
   * @param array $window Retrieve only a given portion
   * @return array|false Returns array on success and false on failure.
   */
  public function find(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("find $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * If group is omitted returns an array of unique tag values of the specified type.
   * If group is specified returns an array of associative arrays containing the grouped result.
   * @param string $type Any tag supported by MPD. Like artist or album.
   * @param Filter|null $filter
   * @param string $group Tag name to group the result by. Like artist or album.
   * @return array|false
   */
  public function list(string $type, Filter $filter = null, string $group = "")
  {
    $m = MPD_CMD_READ_LIST_SINGLE;
    if(!empty($group)){
      $m = MPD_CMD_READ_LIST;
    }

    $type = escape_params([ $type ]);
    return $this->mphpd->cmd("list $type $filter", [
      ($group ? "group" : ""), ($group ?: "")
    ], $m);
  }


  /**
   * List files,directories and playlists in $uri
   * @param string $uri
   * @param bool $metadata Specifies if additional information should be included.
   * @param bool $recursive Specified if files and directories should be listed recursively.
   * @return array|false Returns an array containing the keys `files`, `directories` and `playlists` on success and `false` on failure.
   */
  public function ls(string $uri, bool $metadata = false, bool $recursive = false)
  {

    if($metadata && $recursive){
      $items = $this->mphpd->cmd("listallinfo", [$uri], MPD_CMD_READ_LIST, [ "file", "directory", "playlist" ]);
    }elseif(!$metadata && $recursive){
      $items = $this->mphpd->cmd("listall", [$uri], MPD_CMD_READ_LIST, [ "file", "direcotry", "playlist" ]);
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
   * @param string $uri
   * @return array|false
   */
  public function read_comments(string $uri)
  {
    return $this->mphpd->cmd("readcomments", [$uri], MPD_CMD_READ_LIST);
  }


  /**
   * Returns a picture of $uri by reading embedded pictures from binary tags.
   * @param string $uri
   * @return false|string Binary data on success and false on failure.
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
   * Searches for matching songs and returns an array of associative arrays containing song information.
   * NOT case-sensitive!
   * @see DB::find()
   * @param Filter $filter
   * @param string $sort
   * @param array $window
   * @return array|false
   */
  public function search(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("search $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Update the Database and return the Job-ID.
   * @param string $uri Optional. Only update the given path. Omit or specify an empty string to update everything.
   * @param bool $rescan If set to `true` also rescan unmodified files.
   * @param bool $force If set to `false` and an update Job is already running, just return its ID.
   *
   *                    If true and an update Job is already running it starts another one and returns the ID of the new Job.
   * @return int|false Returns the Job-ID on success or false on failure.
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
