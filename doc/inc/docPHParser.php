<?php
/*
 * Copyright (c) Florian Faber 2023.
 * docPHParser - A docblock parser for PHP in PHP
 * This project is released under the MIT license.
 */

declare(strict_types=1);

function scanAllDir($dir): array
{
  $result = [];
  foreach(scandir($dir) as $filename) {
    if ($filename[0] === '.') continue;
    $filePath = $dir . '/' . $filename;
    if (is_dir($filePath)) {
      foreach (scanAllDir($filePath) as $childFilename) {
        $result[] = $filename . '/' . $childFilename;
      }
    } else {
      $result[] = $filename;
    }
  }
  return $result;
}

class docPHParser
{

  private array $classes = [];


  /**
   * @throws ReflectionException
   */
  public function __construct(array $config = [])
  {

    $base = $config["source"] ?? __DIR__;
    $files = scanAllDir($base);

    $classes = [];

    print_r($files);

    foreach ($files as $file) {

      $a = explode("/", $file);
      $filename = array_pop($a);

      $b = explode(".", $filename);
      $classname = $b[0];

      $namespace = "";
      foreach(explode("\n", file_get_contents($base."/".$file)) as $line){
        $line = trim($line);
        if(strpos($line, "namespace") === 0){
          echo $line."\n";
          $ns = explode(" ", $line);
          $namespace = str_replace(";","", array_pop($ns))."\\";
        }
      }

      echo "Loading $base/$file\n";
      require_once $base."/".$file;

      try{
        $reflection = new ReflectionClass("$namespace$classname");
      }catch (ReflectionException $e){
        continue;
      }

      $this->classes[] = $reflection;

    }

  }

  /**
   * @return ReflectionClass[]
   */
  public function getClasses(): array
  {
    return $this->classes;
  }

}