<?php

namespace FloFaber;

/**
 * This subclass is used for client-to-client communication over MPD
 * @title Channels
 * @usage MphpD::channel(string $name) : Channel
 */
class Channel
{

  /*
   * ######################
   *    CLIENT TO CLIENT
   * ######################
   */

  private MphpD $mphpd;
  private string $name;

  public function __construct(MphpD $mphpd, string $name)
  {
    $this->mphpd = $mphpd;
    $this->name = $name;
  }


  /**
   * Subscribe to the channel.
   * @return bool
   * @throws MPDException
   */
  public function subscribe(): bool
  {
    return $this->mphpd->cmd("subscribe", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Unsubscribe the channel.
   * @return bool
   * @throws MPDException
   */
  public function unsubscribe(): bool
  {
    return $this->mphpd->cmd("unsubscribe", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Returns a list of the channel's messages.
   * @return array|false `Array` containing the messages on success. `False` otherwise.
   * @throws MPDException
   */
  public function readmessages()
  {

    $messages = $this->mphpd->cmd("readmessages", [], MPD_CMD_READ_LIST);
    if($messages === false){ return false; }

    $msgs = [];
    foreach($messages as $message){
      if($message["channel"] === $this->name){
        $msgs[] = $message["message"];
      }
    }

    return $msgs;
  }


  /**
   * Send a message to the channel.
   * @param string $message
   * @return bool
   * @throws MPDException
   */
  public function sendmessage(string $message) : bool
  {
    return $this->mphpd->cmd("sendmessage", [ $this->name, $message ], MPD_CMD_READ_BOOL);
  }
}