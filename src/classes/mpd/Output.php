<?php

namespace FloFaber;

// https://mpd.readthedocs.io/en/latest/protocol.html#audio-output-devices
class Output
{

  private MphpD $mphpd;
  private int $id;


  public function __construct(MphpD $mphpd, int $id)
  {
    $this->mphpd = $mphpd;
    $this->id = $id;
  }


  /**
   * Disable the given output
   * @return bool
   * @throws MPDException
   */
  public function disable() : bool
  {
    return $this->mphpd->cmd("disableoutput", [ $this->id ], MPD_CMD_READ_BOOL);
  }


  /**
   * Enable the given output
   * @return bool
   * @throws MPDException
   */
  public function enable(): bool
  {
    return $this->mphpd->cmd("enableoutput", [ $this->id ], MPD_CMD_READ_BOOL);
  }


  /**
   * Enable/Disable the given output depending on the current state.
   * @return bool
   * @throws MPDException
   */
  public function toggle(): bool
  {
    return $this->mphpd->cmd("toggleoutput", [ $this->id ], MPD_CMD_READ_BOOL);
  }


  /**
   * Set a runtime attribute. Supported values can be retrieved from the `MphpD::outputs()` method.
   * @param string $name
   * @param string $value
   * @return bool
   * @throws MPDException
   */
  public function set(string $name, string $value): bool
  {
    return $this->mphpd->cmd("outputset", [ $this->id, $name, $value ], MPD_CMD_READ_BOOL);
  }


}