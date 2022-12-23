<?php

/*
 * MphpD
 * http://mphpd.org
 *
 * (c) Florian Faber
 * http://www.flofaber.com
 */

declare(strict_types=1);

namespace FloFaber;

// include class-less files
require_once __DIR__ . "/inc/consts.php";
require_once __DIR__ . "/inc/utils.php";

// include needed subclasses
require_once __DIR__ . "/classes/Socket.php"; // Extended by the main class for socket communication
require_once __DIR__ . "/classes/Filter.php"; // FloFaber\Filter

require_once __DIR__ . "/classes/mpd/Channel.php"; // MphpD::channel()
require_once __DIR__ . "/classes/mpd/DB.php"; // MphpD::DB
require_once __DIR__ . "/classes/mpd/Mount.php"; // MphpD::mount()
require_once __DIR__ . "/classes/mpd/Output.php"; // MphpD::output()
require_once __DIR__ . "/classes/mpd/Partition.php"; // MphpD::partition()
require_once __DIR__ . "/classes/mpd/Player.php"; // MphpD::player
require_once __DIR__ . "/classes/mpd/Playlist.php"; // MphpD::playlist()
require_once __DIR__ . "/classes/mpd/Queue.php"; // MphpD::queue
require_once __DIR__ . "/classes/mpd/Status.php"; // MphpD::status
require_once __DIR__ . "/classes/mpd/Sticker.php"; // Mphpd::sticker()



class MphpD extends Socket
{

  private DB $db;
  private Player $player;
  private Queue $queue;
  private Status $status;


  public function __construct(array $options = [])
  {
    $this->db = new DB($this);
    $this->player = new Player($this);
    $this->queue = new Queue($this);
    $this->status = new Status($this);

    parent::__construct($options);
  }


  // Disconnect on destruction
  public function __destruct(){
    $this->disconnect();
  }


  /**
   * Return the DB instance
   * @return DB
   */
  public function db() : DB
  {
    return $this->db;
  }


  /**
   * Return the Player instance
   * @return Player
   */
  public function player() : Player
  {
    return $this->player;
  }


  /**
   * Return the Queue instance
   * @return Queue
   */
  public function queue() : Queue
  {
    return $this->queue;
  }


  /**
   * Return the Status instance
   * @return Status
   */
  public function status() : Status
  {
    return $this->status;
  }


  /**
   * Returns a Playlist instance with the given name or null if the name is empty
   * @param string $name Playlist name. Must not be empty.
   * @return Playlist|null
   */
  public function playlist(string $name): ?Playlist
  {
    try{
      return new Playlist($this, $name);
    }catch (MPDException $e){
      return null;
    }
  }


  /**
   * Returns an Array of associative arrays of all playlists in the playlist directory
   * @return array|false
   * @throws MPDException
   */
  public function playlists()
  {
    return $this->cmd("listplaylists", [], MPD_CMD_READ_LIST);
  }


  /**
   * Return a new output instance
   * @param int $id
   * @return Output
   */
  public function output(int $id) : Output
  {
    return new Output($this, $id);
  }


  /**
   * Returns an Array of associative arrays of all available outputs
   * @return array|false
   * @throws MPDException
   */
  public function outputs()
  {
    return $this->cmd("outputs", [], MPD_CMD_READ_LIST);
  }


  /**
   * Return a new Mount instance
   * @param string $path
   * @return Mount
   */
  public function mount(string $path) : Mount
  {
    return new Mount($this, $path);
  }


  /**
   * Return all mounts.
   * @return array|bool
   * @throws MPDException
   */
  public function mounts()
  {
    return $this->cmd("listmounts", [], MPD_CMD_READ_LIST);
  }


  /**
   * Return neighbors on the network like available SMB servers
   * @return array|bool
   * @throws MPDException
   */
  public function neighbors()
  {
    return $this->cmd("listneighbors", [], MPD_CMD_READ_LIST);
  }


  /**
   * Return a new Partition instance
   * @param string $name
   * @return Partition
   */
  public function partition(string $name) : Partition
  {
    return new Partition($this, $name);
  }


  /**
   * Return a list of all available partitions
   * @return array|bool
   * @throws MPDException
   */
  public function partitions()
  {
    $partitions = $this->cmd("listpartitions", [], MPD_CMD_READ_LIST);
    if($partitions === false){ return false; }
    $ps = [];
    foreach($partitions as $partition){
      $ps[] = $partition["partition"];
    }
    return $ps;
  }


  /**
   * Return a new Channel instance
   * @param string $name
   * @return Channel
   */
  public function channel(string $name = "") : Channel
  {
    return new Channel($this, $name);
  }


  /**
   * Return a list of available channels
   * @throws MPDException
   */
  public function channels()
  {
    return $this->cmd("channels", [], MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Returns a Sticker instance
   * @param string $type
   * @param string $uri
   * @return Sticker
   */
  public function sticker(string $type, string $uri) : Sticker
  {
    return new Sticker($this, $type, $uri);
  }


  /*
   * ################
   *    REFLECTION
   * ################
   */


  public function config()
  {
    return $this->cmd("config");
  }


  public function commands()
  {
    return $this->cmd("commands", [], MPD_CMD_READ_LIST_SINGLE);
  }


  public function notcommands()
  {
    return $this->cmd("notcommands", [], MPD_CMD_READ_LIST_SINGLE);
  }


  public function urlhandlers()
  {
    return $this->cmd("urlhandlers", [], MPD_CMD_READ_LIST_SINGLE);
  }


  public function decoders()
  {
    return $this->cmd("decoders", [], MPD_CMD_READ_LIST);
  }


} // End MphpD

