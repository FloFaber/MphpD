<?php

namespace FloFaber;

require_once __DIR__ . "/../Filter.php";

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

      try{
        $aa = $this->mphpd->cmd("albumart", [$songuri, $offset]);
        $binary_size = $aa["size"];
      }catch (MPDException $e){
        return false;
      }

      $offset = $offset + $this->mphpd->binarylimit();
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
   * @throws MPDException
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
   * @return string|bool Returns the fingerprint on success or false on failure.
   * @throws MPDException
   */
  public function getfingerprint(string $uri)
  {
    $fp = $this->mphpd->cmd("getfingerprint", [$uri]);
    if($fp === false){ return false; }
    return $fp["chromaprint"] ?? false;
  }


  /**
   * Search for songs matching Filter and return an array of associative array of found songs.
   * case-sensitive!
   * @param Filter $filter
   * @param string $sort Tag name to sort by. Like artist. If prefixed with `-` it will be sorted descending.
   *
   *                     If omitted the order is undefined.
   * @param array $window Retrieve only a given portion
   * @return array|false Returns array on success and false on failure.
   * @throws MPDException
   */
  public function find(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("find $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Same as `find()` but this adds the matching song to the Queue.
   * @see DB::find()
   * @param Filter $filter
   * @param string $sort
   * @param array $window
   * @param int $pos Optional. If specified the matched songs will be added to this position in the Queue.
   * @return array|bool
   * @throws MPDException
   */
  public function findadd(Filter $filter, string $sort = "", array $window = [], int $pos = -1)
  {
    return $this->mphpd->cmd("find $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : ""),
      ($pos !== -1 ? "position" : ""), ($pos !== -1 ? $pos : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * If group is omitted returns an array of unique tag values of the specified type.
   * If group is specified returns an array of associative arrays containing the grouped result.
   * @param string $type Any tag supported by MPD. Like artist or album.
   * @param Filter $filter
   * @param string $group Tag name to group the result by. Like artist or album.
   * @return array|bool
   * @throws MPDException
   */
  public function list(string $type, Filter $filter, string $group = "")
  {
    $m = MPD_CMD_READ_LIST_SINGLE;
    if(!empty($group)){
      $m = MPD_CMD_READ_LIST;
    }

    $type = "type ".escape_params([ $type ]);
    return $this->mphpd->cmd("list $type $filter", [
      ($group ? "group" : ""), ($group ?: "")
    ], $m);
  }


  /**
   * Returns all songs and directories in $uri
   * If $metadata is false or omitted returns an array containing all songs and directories in $uri.
   * If $metadata is true returns an array of associative arrays containing all songs in $uri including metadata.
   * @param string $uri
   * @param bool $metadata
   * @return array|bool
   * @throws MPDException
   */
  public function listall(string $uri = "", bool $metadata = false)
  {

    if($metadata === true){
      return $this->mphpd->cmd("listallinfo", [$uri], MPD_CMD_READ_LIST);
    }

    $list = [];
    $ls = $this->mphpd->cmd("listall", [$uri], MPD_CMD_READ_LIST);
    if($ls === false){ return false; }
    foreach($ls as $l){
      $file = $l["file"];
      $list[] = $file;
    }
    return $list;
  }


  /**
   * Returns an array with a "directories"-key and a "files" key containing the directories/files of $uri.
   * @param string $uri Can be a relative path or a URI understood by one of the storage plugins.
   * @param bool $metadata If true metadata will be included in the information.
   * @return array|false Array on success or false on failure.
   *                     <pre>Array [
   *                       "directories" => Array [
   *                         Array [
   *                           "directory" => "dirname",
   *                           "last-modified" => "2022-10-03T16:26:58Z"
   *                         ], Array [
   *                           "directory" => "dir2", ...
   *                         ]
   *                       ],
   *                       "files" => Array [
   *                         Array [
   *                           "file" => "song1.mp3",
   *                           "size" => 123456,
   *                           "last-modified" => "2023-01-01T23:59:01Z",
   *                           {OPTIONAL METADATA}
   *                         ], Array [
   *                           "file" => "song2.mp3", ...
   *                         ]
   *                       ]
   *                     ]</pre>
   * @throws MPDException
   */
  public function listfiles(string $uri, bool $metadata = false)
  {

    $cmd = "listfiles";
    if($metadata === true){
      $cmd = "lsinfo";
    }

    $files = $directories = [];
    $lfs = $this->mphpd->cmd($cmd, [$uri], MPD_CMD_READ_LIST);
    if($lfs === false){ return false; }

    foreach($lfs as $lf){
      if(isset($lf["file"])){
        $files[] = $lf;
      }elseif(isset($lf["directory"])){
        $directories[] = $lf;
      }
    }
    return [
      "directories" => $directories,
      "files" => $files
    ];
  }


  /**
   * Read "comments" from the specified file.
   * The meaning of these "comments" depend on the codec. For an OGG file this lists the vorbis commands.
   * @param string $uri
   * @return array|bool
   * @throws MPDException
   */
  public function readcomments(string $uri)
  {
    return $this->mphpd->cmd("readcomments", [$uri], MPD_CMD_READ_LIST);
  }


  /**
   * Returns a picture of $uri by reading embedded pictures from binary tags.
   * @param string $uri
   * @return false|string Binary data on success and false on failure.
   */
  public function readpicture(string $uri)
  {
    $offset = 0;
    $binary_data = "";
    do{

      try{
        $aa = $this->mphpd->cmd("readpicture", [$uri, $offset]);
        $binary_size = $aa["size"];
      }catch (MPDException $e){
        return false;
      }

      $offset = $offset + $this->mphpd->binarylimit();
      $binary_data .= $aa["binary_data"];

    }while($offset < $binary_size);

    return $binary_data;
  }


  /**
   * Searches for matching songs and returns an array of associative arrays containing song information.
   * NOT case-sensitive.
   * @see DB::find()
   * @param Filter $filter
   * @param string $sort
   * @param array $window
   * @return array|bool
   * @throws MPDException
   */
  public function search(Filter $filter, string $sort = "", array $window = [])
  {
    return $this->mphpd->cmd("search $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : "")
    ], MPD_CMD_READ_LIST);
  }


  /**
   * Same as `search()` but adds the songs into the Queue at position $pos.
   * @see DB::search()
   * @param Filter $filter
   * @param string $sort
   * @param array $window
   * @param int $position
   * @return bool
   * @throws MPDException
   */
  public function searchadd(Filter $filter, string $sort, array $window = [], int $position = -1) : bool
  {
    return $this->mphpd->cmd("searchadd $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : ""),
      ($position !== -1 ? "position" : ""), ($position !== -1 ? $position : "")
    ]) !== false;
  }


  /**
   * Same as `search()` but adds the songs into the Playlist $playlist at position $pos.
   * @see DB::search()
   * @param string $name Name of the playlist. If playlist does not exist it will be created.
   * @param Filter $filter
   * @param string $sort
   * @param array $window
   * @param int $position
   * @return bool
   * @throws MPDException
   */
  public function searchaddpl(string $name, Filter $filter, string $sort = "", array $window = [], int $position = -1) : bool
  {
    $name = escape_params([ $name ]);
    return $this->mphpd->cmd("searchaddpl $name $filter", [
      ($sort ? "sort" : ""), ($sort ?: ""),
      ($window ? "window" : ""), ($window ? pos_or_range($window) : ""),
      ($position !== -1 ? "position" : ""), ($position !== -1 ? $position : "")
    ]) !== false;
  }


  /**
   * Update the Database. Returns Job-ID.
   * @param string $uri Optional. Only update the given path.
   * @param bool $rescan If true rescans also unmodified files.
   * @param bool $force If false and an update Job is already running, just returns its ID.
   *
   *                    If true and an update Job is already running it starts another one and returns the ID of the new Job.
   * @return int|false Returns Job-ID on success or false on failure.
   * @throws MPDException
   */
  public function update(string $uri = "", bool $rescan = false, bool $force = false)
  {

    $cmd = "update";
    if($rescan === true){
      $cmd = "rescan";
    }

    // if a job is already running but $force is false return the current Job ID
    $job = $this->mphpd->status()->get([ "updating_db" ]);
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
