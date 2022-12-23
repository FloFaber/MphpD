<?php

namespace FloFaber;

class Mount
{

  private MphpD $mphpd;
  private string $path;

  public function __construct(MphpD $mphpd, string $path)
  {
    $this->mphpd = $mphpd;
    $this->path = $path;
  }


  public function mount(string $uri) : bool
  {
    return $this->mphpd->cmd("mount", [ $this->path, $uri ]) !== false;
  }


  public function unmount()
  {
    return $this->mphpd->cmd("unmount", [ $this->path ]);
  }

}