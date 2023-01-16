<?php

namespace FloFaber;

/**
 * This subclass is used to create, switch and configure [partitions](https://mpd.readthedocs.io/en/latest/protocol.html#partition-commands)
 * @title Partitions
 * @usage MphpD::partition(string $name) : Partition
 */
class Partition
{

  private MphpD $mphpd;
  private string $name;

  public function __construct(MphpD $mphpd, string $name)
  {
    $this->mphpd = $mphpd;
    $this->name = $name;
  }

  /**
   * Switch the client to the given partition
   * @return bool
   * @throws MPDException
   */
  public function switch(): bool
  {
    return $this->mphpd->cmd("partition", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Create a new partition
   * @return bool
   * @throws MPDException
   */
  public function create(): bool
  {
    return $this->mphpd->cmd("newpartition", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Delete a given partition
   * @return bool
   * @throws MPDException
   */
  public function delete(): bool
  {
    return $this->mphpd->cmd("delpartition", [ $this->name ], MPD_CMD_READ_BOOL);
  }




  /**
   * Move a specific output to the current partition
   * @param string $name Name of the output
   * @return bool
   * @throws MPDException
   */
  public function moveoutput(string $name): bool
  {
    return $this->mphpd->cmd("moveoutput", [ $name ], MPD_CMD_READ_BOOL);
  }

}