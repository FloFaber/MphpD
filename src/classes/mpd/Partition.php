<?php

namespace FloFaber;

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
    return $this->mphpd->cmd("partition", [ $this->name ]) !== false;
  }


  /**
   * Create a new partition
   * @return bool
   * @throws MPDException
   */
  public function create(): bool
  {
    return $this->mphpd->cmd("newpartition", [ $this->name ]) !== false;
  }


  /**
   * Delete a given partition
   * @return bool
   * @throws MPDException
   */
  public function delete(): bool
  {
    return $this->mphpd->cmd("delpartition", [ $this->name ]) !== false;
  }




  /**
   * Move a specific output to the current partition
   * @param string $name Name of the output
   * @return bool
   * @throws MPDException
   */
  public function moveoutput(string $name): bool
  {
    return $this->mphpd->cmd("moveoutput", [ $name ]) !== false;
  }

}