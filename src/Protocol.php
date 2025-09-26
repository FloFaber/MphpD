<?php declare(strict_types=1);
/*
 * MphpD
 * http://mphpd.org
 * Copyright (c) 2023-2025 Florian Faber
 * http://www.flofaber.com
 */

namespace FloFaber\MphpD;

class Protocol
{

  private MphpD $mphpd;

  /**
   * This class is not intended for direct usage.
   * Use `MphpD::protocol()` instead to retrieve an instance of this class.
   * @param MphpD $mphpd
   */
  public function __construct(MphpD $mphpd)
  {
    $this->mphpd = $mphpd;
  }


  /**
   * Shows a list of enabled protocol features.
   * Available features:
   *   hide_playlists_in_root: disables the listing of stored playlists for MphpD::DB::ls().
   *
   * @return false|array
   */
  public function get() : false|array
  {
    return $this->mphpd->cmd("protocol", mode: MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Lists all available protocol features.
   * @return false|array
   */
  public function available() : false|array
  {
    return $this->mphpd->cmd("protocol available", mode: MPD_CMD_READ_LIST_SINGLE);
  }


  /**
   * Disables one or more features.
   * @param array|null $features
   * @return bool
   */
  public function disable(?array $features = null) : bool
  {
    return $this->mphpd->cmd("protocol disable", $features, MPD_CMD_READ_BOOL);
  }


  /**
   * Disables all protocol features.
   * @return bool
   */
  public function disable_all() : bool
  {
    return $this->mphpd->cmd("protocol clear", mode: MPD_CMD_READ_BOOL);
  }


  /**
   * Enables one or more features
   * @param array|null $features
   * @return bool
   */
  public function enable(?array $features = null) : bool
  {
    return $this->mphpd->cmd("protocol enable", $features, MPD_CMD_READ_BOOL);
  }


  /**
   * Enables all protocol features.
   * @return bool
   */
  public function enable_all() : bool
  {
    return $this->mphpd->cmd("protocol all", mode: MPD_CMD_READ_BOOL);
  }


}