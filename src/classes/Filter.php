<?php

namespace FloFaber;

/**
 * Class to handle MPD filters. It takes care of parsing and escaping.
 * See: https://mpd.readthedocs.io/en/latest/protocol.html#filters for more
 * @Link: https://mphpd.org/doc/filters
 */
class Filter
{

  private string $tag;
  private string $operator;
  private string $value;


  /**
   * Creates a filter
   * @param string $tag Tag to be searched for. Like artist, title,...
   * @param string $operator Comparison operator. Like ==, contains, ~=,...
   * @param string $value The value to be search for. Unescaped. Gets escaped automatically
   */
  public function __construct(string $tag, string $operator, string $value)
  {
    $this->tag = $tag;
    $this->operator = $operator;
    $this->value = $value;

    $this->value = escape_params([ $this->value ], MPD_ESCAPE_DOUBLE_QUOTES);
  }


  /**
   * Returns the generated Filter string.
   * @return string
   */
  public function __toString()
  {
    return "\"($this->tag $this->operator $this->value)\"";
  }

} // End MPDFilter

