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
 * This subclass is used to mount and unmount directories.
 * @title Mounts
 * @usage MphpD::mount(string $name) : Mount
 */
class Mount
{

  private MphpD $mphpd;
  private string $path;

  public function __construct(MphpD $mphpd, string $path)
  {
    $this->mphpd = $mphpd;
    $this->path = $path;
  }


  /**
   * Mount $uri to path
   * @param string $uri The URI to mount
   * @return bool
   */
  public function mount(string $uri) : bool
  {
    return $this->mphpd->cmd("mount", [ $this->path, $uri ], MPD_CMD_READ_BOOL);
  }


  /**
   * Unmount the path
   * @return bool
   */
  public function unmount(): bool
  {
    return $this->mphpd->cmd("unmount", [ $this->path ], MPD_CMD_READ_BOOL);
  }

}