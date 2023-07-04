<?php
/*
 * MphpD
 * http://mphpd.org
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

/*
 * This script is used to extract doc-blocks from MphpDs source code and convert them into nice markdown
 * which is then used by pagenode (pagenode.org) to render a nice HTML page.
 * That whole setup is far too complicated in my opinion.
 * ---
 * Needless to say there are still several things to do:
 * * Cleanup, this code is a huge mess.
 * * Preferably stop using Pagenode and instead build static HTML sites directly. Maybe we could then
 *   also build a PDF and include that in the releases. Would be nice.
 */


require_once __DIR__ . "/inc/docPHParser.php";
require_once __DIR__ . "/../vendor/autoload.php";

use phpDocumentor\Reflection\DocBlockFactory;

// we require a version number
if(empty($argv[1])){
  echo "Usage: build.php <version>\n";
  die(1);
}

define("VERSION", $argv[1]);


const CONFIG = [
  "source" => __DIR__ . "/../src/"
];


$docparser = new docPHParser(CONFIG);
$pd = new Parsedown();
$factory  = DocBlockFactory::createInstance();



// include custom docs
// source -> target
$include_docs = [
//  [ "src" => __DIR__ . "/../README.md", "dst" => __DIR__ . "/www/build/".VERSION."/index.md" ],
  [ "src" => __DIR__ . "/guides/",      "dst" => __DIR__ . "/www/build/".VERSION."/guides/" ]
];

rrmdir(__DIR__ . "/www/build/".VERSION);

/*// rebuild www dir


mkdir(__DIR__ . "/www/");
mkdir(__DIR__ . "/www/build/");*/
mkdir(__DIR__ . "/www/build/".VERSION."/");
mkdir(__DIR__ . "/www/build/".VERSION."/methods/");
mkdir(__DIR__ . "/www/build/".VERSION."/guides/");
mkdir(__DIR__ . "/www/build/".VERSION."/classes/");


// iterate through each class we found
foreach($docparser->getClasses() as $class){

  $class_info = [];

  $tmp = explode("\\", $class->getName());
  $class_info["name"] = array_pop($tmp); unset($tmp);
  $class_info["namespace"] = $class->getNamespaceName();
  $class_info["methods"] = $class->getMethods(Reflectionmethod::IS_PUBLIC);
  $class_info["docblock"] = $class->getDocComment() ? $factory->create($class->getDocComment()) : null;
  $class_info["summary"] = $class_info["docblock"]?->getSummary();
  $class_info["description"] = $class_info["docblock"]?->getDescription();
  $class_info["text"] = $class_info["summary"]."\n".$class_info["description"];
  $class_info["example"] = $class_info["docblock"]?->getTagsByName("example");
  $class_info["template_file"] = __DIR__ . "/templates/class.template.html";

  $template_class = file_get_contents($class_info["template_file"]);
  $class_info["methods_text"] = ""; // holding filled templates of all methods in this class

  //continue;

  foreach($class_info["methods"] as $method){

    $method_info = [];

    $method_info["name"] = $method->getName();
    $method_info["params"] = $method->getParameters();
    $method_info["docblock"] = $method->getDocComment() ? $factory->create($method->getDocComment()) : null;
    $method_info["summary"] = $method_info["docblock"]?->getSummary();
    $method_info["description"] = $method_info["docblock"]?->getDescription();
    $method_info["text"] = $method_info["summary"]."\n".$method_info["description"];
    $method_info["template_file"] = __DIR__ . "/templates/method.template.html";
    $method_info["return_text"] = $method_info["docblock"]?->getTagsByName("return")[0] ?? "";
    $method_info["return_type"] = $method->hasReturnType() ? $method->getReturnType()->getName() : "void";

    $template_method = file_get_contents($method_info["template_file"]);

    if($method_info["name"] === "__destruct"){ continue; }

    $method_info["params_text"] = "";
    // make parameter text
    foreach($method_info["params"] as $param){
      $method_info["params_text"] .= "<h5>\$".$param->getName()."</h5>";
    }
    if(!$method_info["params"]){ $method_info["params_text"] = "None."; }

    // www "usage"-line
    $usage = $class_info["name"]."::".$method_info["name"]."(";
    foreach ($method_info["params"] as $param) {
      $usage .= $param->getType()." ";
      $usage .= '$'.$param->getName();
      try{
        $default = $param->getDefaultValue();
        if(empty($default) AND is_string($default)){
            $default = "''";
        }elseif(is_array($default)){
            $default = "[]";
        }
        $usage .= " = $default";
      }catch (ReflectionException $e){

      } finally {
        if($param !== $method_info["params"][count($method_info["params"])-1])
          $usage .= ", ";
      }
    }
    $usage .= ")";

    if($method_info["return_type"]){
      $usage .= " : ".$method_info["return_type"];
    }
    $method_info["usage"] = $usage; unset($usage);

    foreach($method_info as $k => $v){
      if(gettype($v) === "array"){ continue; }
      echo "Processing $k\n";
      if(!str_contains($template_method, "{{method.$k}}")){ continue; }
      $template_method = str_replace("{{method.$k}}", (string)$v, $template_method);
    }

    $class_info["methods_text"] .= $template_method;
    file_put_contents(__DIR__ . "/www/build/".VERSION."/methods/".$class_info["name"]."-".$method_info["name"].".html", $template_method);

  }


  foreach($class_info as $k => $v){
    if(gettype($v) === "array"){ continue; }
    if(!str_contains($template_class, "{{class.$k}}")){ continue; }
    $template_class = str_replace("{{class.$k}}", $v, $template_class);
  }

  file_put_contents(__DIR__ . "/www/build/".VERSION."/classes/".$class_info["name"].".html", $template_class);

  if(VERSION !== "test"){
    unlink(__DIR__ . "/www/build/latest");
    chdir(__DIR__ . "/www/build/");
    symlink(VERSION, "latest");
  }

  foreach ($include_docs as $include_doc) {
    $s = $include_doc["src"]; //source
    $d = $include_doc["dst"]; //destination

    if(is_dir($s)){
      recurse_copy($s, $d);
    }else{
      copy($s, $d);
    }
  }

  file_put_contents(__DIR__ . "/www/build/".VERSION."/index.html", $pd->text(file_get_contents(__DIR__ . "/../README.md")));

}



/*
 * UTILITY FUNCTIONS
*/

function rrmdir($dir): void
{
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
          rrmdir($dir. DIRECTORY_SEPARATOR .$object);
        else
          unlink($dir. DIRECTORY_SEPARATOR .$object);
      }
    }
    rmdir($dir);
  }
}

function recurse_copy($src,$dst): void
{
  $dir = opendir($src);
  @mkdir($dst);
  while(false !== ( $file = readdir($dir)) ) {
    if (( $file != '.' ) && ( $file != '..' )) {
      if ( is_dir($src . '/' . $file) ) {
        recurse_copy($src . '/' . $file,$dst . '/' . $file);
      }
      else {
        copy($src . '/' . $file,$dst . '/' . $file);
      }
    }
  }
  closedir($dir);
}
