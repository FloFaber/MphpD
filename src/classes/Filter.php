<?php

namespace FloFaber;

/**
 * Class to handle MPD filters. It takes care of parsing and escaping.
 * See: https://mpd.readthedocs.io/en/latest/protocol.html#filters for more
 * @Link: https://mphpd.org/doc/filters
 */
class Filter
{

  private array $tags = [];
  private array $operators = [];
  private array $values = [];


  /**
   * Creates a new filter
   * @param string $tag Tag to be searched for. Like artist, title,...
   * @param string $operator Comparison operator. Like ==, contains, ~=,...
   * @param string $value The value to search for. Unescaped.
   */
  public function __construct(string $tag, string $operator, string $value)
  {
    $this->and($tag, $operator, $value);
  }


  /**
   * Used to chain multiple filters with AND
   * @param string $tag
   * @param string $operator
   * @param string $value
   * @return $this
   */
  public function and(string $tag, string $operator, string $value): Filter
  {
    $value = escape_params([ $value ], MPD_ESCAPE_DOUBLE_QUOTES);
    $this->tags[] = $tag;
    $this->operators[] = $operator;
    $this->values[] = $value;
    return $this;
  }


  /**
   * Generate and return the Filter string.
   * @return string
   */
  public function __toString()
  {
    $s = "\"";
    if(count($this->tags) > 1){
      $s .= "(";
    }

    for($i = 0; $i < count($this->tags); $i++){

      $t = $this->tags[$i];
      $o = $this->operators[$i];
      $v = $this->values[$i];

      if($i > 0){
        $s .= " AND ";
      }

      $s .= "($t $o $v)";

    }

    if(count($this->tags) > 1){
      $s .= ")";
    }

    return $s."\"";
  }

} // End MPDFilter

