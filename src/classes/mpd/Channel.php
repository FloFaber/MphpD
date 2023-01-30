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
  private static array $unread_messages = [];

  public function __construct(MphpD $mphpd, string $name)
  {
    $this->mphpd = $mphpd;
    $this->name = $name;
  }


  /**
   * Subscribe to the channel.
   * @return bool
   */
  public function subscribe(): bool
  {
    return $this->mphpd->cmd("subscribe", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Unsubscribe the channel.
   * @return bool
   */
  public function unsubscribe(): bool
  {
    return $this->mphpd->cmd("unsubscribe", [ $this->name ], MPD_CMD_READ_BOOL);
  }


  /**
   * Returns a list of the channel's messages.
   * @return array|false `Array` containing the messages on success. `False` otherwise.
   */
  public function read()
  {

    $messages = $this->mphpd->cmd("readmessages", [], MPD_CMD_READ_LIST);
    if($messages === false){ return false; }

    $msgs = [];

    // loop through the unread messages and add those in the wanted channel to the $msgs array.
    foreach(Channel::$unread_messages as $key => $unread_message){
      if($unread_message["channel"] === $this->name){
        $msgs[] = $unread_message["message"];

        // remove message and re-index
        unset(Channel::$unread_messages[$key]);
        Channel::$unread_messages = array_values(Channel::$unread_messages);
      }
    }

    foreach($messages as $message){
      if($message["channel"] === $this->name){
        $msgs[] = $message["message"];
      }else{
        // if the channel is not the channel we want, save the message in case the user wants to retrieve later on
        Channel::$unread_messages[] = $message;
      }
    }

    return $msgs;
  }


  /**
   * Send a message to the channel.
   * @param string $message
   * @return bool
   */
  public function send(string $message) : bool
  {
    return $this->mphpd->cmd("sendmessage", [ $this->name, $message ], MPD_CMD_READ_BOOL);
  }
}