<?php
/*
 * MphpD
 * http://mphpd.org
 *
 * Copyright (c) 2023 Florian Faber
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
require_once __DIR__ . "/classes/mpd/Output.php"; // MphpD::output()
require_once __DIR__ . "/classes/mpd/Partition.php"; // MphpD::partition()
require_once __DIR__ . "/classes/mpd/Player.php"; // MphpD::player
require_once __DIR__ . "/classes/mpd/Playlist.php"; // MphpD::playlist()
require_once __DIR__ . "/classes/mpd/Queue.php"; // MphpD::queue
require_once __DIR__ . "/classes/mpd/Sticker.php"; // Mphpd::sticker()


/**
 * The Main MphpD class.
 * @title MphpD
 * @usage new MphpD(array $config = []) : MphpD
 */
class MphpD extends Socket
{

  private DB $db;
  private Player $player;
  private Queue $queue;


  public function __construct(array $options = [])
  {
    $this->db = new DB($this);
    $this->player = new Player($this);
    $this->queue = new Queue($this);

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
   */
  public function outputs()
  {
    return $this->cmd("outputs", [], MPD_CMD_READ_LIST);
  }


  /**
   * Return neighbors on the network like available SMB servers
   * @return array|bool
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
   * @return array|false
   */
  public function partitions()
  {
    return $this->cmd("listpartitions", [], MPD_CMD_READ_LIST_SINGLE);
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
   */
  public function channels()
  {
    return $this->cmd("channels", [], MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Clears the current error
   * @return bool
   */
  public function clear_error() : bool
  {
    return $this->cmd("clearerror", [], MPD_CMD_READ_BOOL);
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
   */
  public function status(array $items = [])
  {
    $status = $this->cmd("status");

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
   */
  public function stats(array $items = [])
  {
    $stats = $this->cmd("stats");

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


  /**
   * Return all mounts.
   * @return array|bool
   */
  public function mounts()
  {
    return $this->cmd("listmounts", [], MPD_CMD_READ_LIST);
  }


  /**
   * Mount $uri to path
   * @param string $path
   * @param string $uri The URI to mount
   * @return bool
   */
  public function mount(string $path, string $uri) : bool
  {
    return $this->cmd("mount", [ $path, $uri ], MPD_CMD_READ_BOOL);
  }


  /**
   * Unmount the path
   * @param string $path
   * @return bool
   */
  public function unmount(string $path): bool
  {
    return $this->cmd("unmount", [ $path ], MPD_CMD_READ_BOOL);
  }


  /*
   * ################
   *    REFLECTION
   * ################
   */


  /**
   * Returns an associative array of configuration values.
   * This function is only available for client connected via Unix Socket!
   * @return array|false
   */
  public function config()
  {
    return $this->cmd("config");
  }


  /**
   * Returns a list of all available commands.
   * @return array|false
   */
  public function commands()
  {
    return $this->cmd("commands", [], MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Returns a list of all not-available commands.
   * @return array|false
   */
  public function notcommands()
  {
    return $this->cmd("notcommands", [], MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Returns a list of all available urlhandlers. Like smb://, sftp://, http://...
   * @return array|bool
   */
  public function urlhandlers()
  {
    return $this->cmd("urlhandlers", [], MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Returns a list of available decoder plugins and their supported suffixes and mimetypes.
   * @return array|false
   */
  public function decoders()
  {
    return $this->cmd("decoders", [], MPD_CMD_READ_LIST);
  }


} // End MphpD

