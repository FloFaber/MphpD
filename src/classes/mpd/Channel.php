<?php

namespace FloFaber;

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
   * Subscribe to a specific channel
   * @return bool
   * @throws MPDException
   */
  public function subscribe(): bool
  {
    return $this->mphpd->cmd("subscribe", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Unsubscribe a channel
   * @return bool
   * @throws MPDException
   */
  public function unsubscribe(): bool
  {
    return $this->mphpd->cmd("unsubscribe", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Read message.
   * If $name was specified only the messages of this specific channel will be returned.
   * If $name was omitted all available message will be returned.
   * @return array|false
   * @throws MPDException
   */
  public function readmessages()
  {

    $messages = $this->mphpd->cmd("readmessages", [], MPD_CMD_READ_LIST);
    if($messages === false){ return false; }
    if(empty($this->name)){
      return $messages;
    }

    $msgs = [];
    foreach($messages as $message){
      if($message["channel"] === $this->name){
        $msgs[] = $message;
      }
    }

    return $msgs;

  }


  /**
   * Send a message to the specified channel.
   * @param string $message
   * @return array|bool
   * @throws MPDException
   */
  public function sendmessage(string $message)
  {
    return $this->mphpd->cmd("sendmessage", [ $this->name, $message ]);
  }
}