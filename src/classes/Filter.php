<?php

namespace FloFaber;

/**
 * Class to handle MPD filters. It takes care of parsing and escaping.
 * See: https://mpd.readthedocs.io/en/latest/protocol.html#filters for more
 * @Link: https://mphpd.org/doc/filters
 */
class Filter
{

  private string $f;


  /**
   * Creates a new filter
   * @param string $tag Tag to be searched for. Like artist, title,...
   * @param string $operator Comparison operator. Like ==, contains, ~=,...
   * @param string $value The value to search for. Unescaped.
   */
  public function __construct(string $tag, string $operator, string $value)
  {
    $value = escape_params([ $value ], MPD_ESCAPE_DOUBLE_QUOTES);
    $this->f = "\"($tag $operator $value)\"";
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
    $this->f = "(".$this->f." AND ".$this->make_str($tag, $operator, $value).")";
    return $this;
  }


  private function make_str(string $tag, string $operator, string $value): string
  {
    $value = escape_params([ $value ], MPD_ESCAPE_DOUBLE_QUOTES);
    return "\"($tag $operator $value)\"";
  }

  /**
   * Returns the generated Filter string.
   * @return string
   */
  public function __toString()
  {
    return $this->f;
  }

} // End MPDFilter

