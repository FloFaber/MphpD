<?php

namespace FloFaber;


/**
 * This subclass is used to control MPDs playback.
 * You may also want to have a look at the [MPD documentation](https://mpd.readthedocs.io/en/latest/protocol.html#playback-options).
 * @usage MphpD::player() : Player
 * @title The Player
 */

class Player
{

  private MphpD $mphpd;
  public function __construct(MphpD $mphpd)
  {
    $this->mphpd = $mphpd;
  }


  /**
   * Enables/Disables the consume mode
   * @param int $state One of the following:
   *
   *                   * MPD_STATE_ON - Enables consume mode
   *
   *                   * MPD_STATE_OFF - Disables consume mode
   *
   *                   * MPD_STATE_ONESHOT - Enables consume mode for a single song.
   *                                         This is only supported on MPD version 0.24 and newer.
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function consume(int $state) : bool
  {
    if($state === MPD_STATE_ONESHOT && $this->mphpd->version_bte("0.24")){
      $state = "oneshot";
    }elseif($state === MPD_STATE_ONESHOT){
      return $this->mphpd->set_error(new MPDException("Unsupported state: oneshot."));
    }

    return $this->mphpd->cmd("consume", [ $state ], MPD_CMD_READ_BOOL);
  }


  /**
   * Sets crossfade to $seconds seconds.
   * @param int $seconds
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function crossfade(int $seconds) : bool
  {
    return $this->mphpd->cmd("crossfade", [$seconds], MPD_CMD_READ_BOOL);
  }


  /**
   * Sets the threshold at which songs will be overlapped.
   * See https://mpd.readthedocs.io/en/latest/user.html#mixramp for more information
   * @param int $dB
   * @return bool Returns true on success and false on failure.
   * @throws MPDException
   */
  public function mixramp_db(int $dB) : bool
  {
    return $this->mphpd->cmd("mixrampdb", [$dB], MPD_CMD_READ_BOOL);
  }


  /**
   * @param float $seconds
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function mixramp_delay(float $seconds) : bool
  {
    return $this->mphpd->cmd("mixrampdelay", [$seconds], MPD_CMD_READ_BOOL);
  }


  /**
   * Specified if MPD should play the queue in random order
   * @param int $state Either `MPD_STATE_OFF` or `MPD_STATE_ON`
   * @return bool Returns true on success and false on failure.
   * @throws MPDException
   */
  public function random(int $state) : bool
  {
    return $this->mphpd->cmd("random", [$state], MPD_CMD_READ_BOOL);
  }


  /**
   * Specifies if MPD should start from the top again when reaching the end of the queue.
   * @param int $state Either `MPD_STATE_OFF` or `MPD_STATE_ON`
   * @return bool Returns true on success and false on failure.
   * @throws MPDException
   */
  public function repeat(int $state) : bool
  {
    return $this->mphpd->cmd("repeat", [$state], MPD_CMD_READ_BOOL);
  }


  /**
   * Sets volume to $volume or returns the current volume if $volume is omitted.
   * @param int $volume If specified the current volume is set to $volume.
   *
   *                    If omitted the current volume is returned.
   * @return int|bool Returns `true` on success, `false` on failure and `int` if $volume was omitted.
   * @throws MPDException
   */
  public function volume(int $volume = -1)
  {
    // if no volume is specified return the current volume
    if($volume === -1){
      // on version 0.23 or higher use the new `getvol` command. Otherwise, get the volume from `status`.
      if($this->mphpd->version_bte("0.23")){
        $v = $this->mphpd->cmd("getvol");
        if($v !== false){ $v = $v["volume"]; }
      }else{
        $v = $this->mphpd->status([ "volume" ]);
      }

      // return volume if valid and -1 otherwise.
      return ($v !== false AND $v !== NULL) ? $v : -1;
    }
    return $this->mphpd->cmd("setvol", [$volume], MPD_CMD_READ_BOOL);
  }


  /**
   * Enables/Disables the single-mode. If enabled MPD will play the same song over and over.
   * @return bool
   * @throws MPDException
   * @param int $state One of the following:
   *
   *                   * MPD_STATE_ON - Enables single mode
   *
   *                   * MPD_STATE_OFF - Disables single mode
   *
   *                   * MPD_STATE_ONESHOT - Enables single mode for only a single time.
   *                     This is only supported on MPD version 0.21 and newer.
   */
  public function single(int $state) : bool
  {

    if($state === MPD_STATE_ONESHOT && $this->mphpd->version_bte("0.21")){
      $state = "oneshot";
    }elseif($state === MPD_STATE_ONESHOT){
      return $this->mphpd->set_error(new MPDException("Unsupported state: oneshot."));
    }

    return $this->mphpd->cmd("single", [$state], MPD_CMD_READ_BOOL);
  }


  /**
   * Specifies whether MPD shall adjust the volume of songs played using ReplayGain tags.
   * @param string $mode One of `off`, `track`, `album`, `auto`
   * @return bool Returns true on success and false on failure.
   * @throws MPDException
   */
  public function replay_gain_mode(string $mode): bool
  {
    return $this->mphpd->cmd("replay_gain_mode", [$mode], MPD_CMD_READ_BOOL);
  }


  /**
   * @return array|false
   * @throws MPDException
   */
  public function replay_gain_status()
  {
    return $this->mphpd->cmd("replay_gain_status");
  }



  /*
   * ######################
   *    PLAYBACK CONTROL
   * ######################
   */

  /**
   * Returns an associative array containing information about the currently playing song.
   * @return array|false
   * @throws MPDException
   */
  public function current_song()
  {
    return $this->mphpd->cmd("currentsong");
  }

  /**
   * Plays the next song in the Queue
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function next() : bool
  {
    return $this->mphpd->cmd("next", [], MPD_CMD_READ_BOOL);
  }


  /**
   * Pause or resume playback.
   * @param int $state Optional. One of the following:
   *
   *                   * MPD_STATE_ON - Pause
   *
   *                   * MPD_STATE_OFF - Resume
   *
   *                   If omitted the pause state is toggled.
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function pause(int $state = -1) : bool
  {

    return $this->mphpd->cmd("pause", [($state !== -1 ? $state : "")], MPD_CMD_READ_BOOL);
  }


  /**
   * Plays the song position $pos in the Queue
   * @param int $pos Song position. Starting at 0
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function play(int $pos = -1) : bool
  {

    $state = $this->mphpd->status([ "state" ]);

    // if no position is given and the player is stopped -> start playing at the first song.
    if($pos === -1 AND $state === "stop"){
      $pos = 0;

      // if no position is given and the player is paused -> unpause
    }elseif($pos === -1 AND $state === "pause"){
      return $this->pause(0);
    }

    // otherwise play the given song
    return $this->mphpd->cmd("play", [ $pos !== -1 ? $pos : 0 ], MPD_CMD_READ_BOOL);
  }


  /**
   * Begins playing the playlist at song $id
   * @param int $id
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function play_id(int $id) : bool
  {
    return $this->mphpd->cmd("playid", [$id], MPD_CMD_READ_BOOL);
  }


  /**
   * Plays the previous song in the Queue
   * @throws MPDException
   */
  public function previous() : bool
  {
    return $this->mphpd->cmd("previous", [], MPD_CMD_READ_BOOL);
  }


  /**
   * Seeks to $seconds of song $songpos in the Queue.
   * @param int $songpos
   * @param int|float $time
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function seek(int $songpos, $time) : bool
  {
    return $this->mphpd->cmd("seek", [ $songpos, $time ], MPD_CMD_READ_BOOL);
  }


  /**
   * Seeks to $seconds of song $songid
   * @param int $songid
   * @param float $time
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function seek_id(int $songid, float $time) : bool
  {
    return $this->mphpd->cmd("seekid", [ $songid, $time ], MPD_CMD_READ_BOOL);
  }


  /**
   * Seeks to $seconds of the current song.
   * @param string|int|float $time If prefixed with `+` or `-` the time is relative to the current playing position.
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function seek_cur($time) : bool
  {
    return $this->mphpd->cmd("seekcur", [$time], MPD_CMD_READ_BOOL);
  }


  /**
   * Stops playing.
   * @return bool Returns true on success and false on failure
   * @throws MPDException
   */
  public function stop() : bool
  {
    return $this->mphpd->cmd("stop", [], MPD_CMD_READ_BOOL);
  }


}
