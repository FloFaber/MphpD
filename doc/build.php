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
  echo "Version number required for build!";
  die(1);
}

define("VERSION", $argv[1]);


const CONFIG = [
  "source" => __DIR__ . "/../src/"
];


$docparser = new docPHParser(CONFIG);

$factory  = DocBlockFactory::createInstance();



// include custom docs
// source -> target
$include_docs = [
  [ "src" => __DIR__ . "/../README.md", "dst" => __DIR__ . "/www/build/".VERSION."/index.md" ],
  [ "src" => __DIR__ . "/guides/",      "dst" => __DIR__ . "/www/build/".VERSION."/guides/" ]
];

/*// rebuild www dir
rrmdir(__DIR__ . "/www/build");

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
    $method_info["return_text"] = $method_info["docblock"]?->getTagsByName("return");
    $method_info["return_type"] = $method->hasReturnType() ? $method->getReturnType() : "void";

    if($method_info["name"] === "__destruct"){ continue; }


    // www "usage"-line
    $usage = $method_info["name"]."::".$method->getName()."(";
    foreach ($method_info["params"] as $param) {
      $usage .= $param->getType()." ";
      $usage .= '$'.$param->getName();
      try{
        $default = $param->getDefaultValue();
        if(empty($default) AND is_string($default)){ $default = "''"; }
        $usage .= " = $default";
      }catch (ReflectionException $e){

      } finally {
        if($param !== $method_info["params"][count($method_info["params"])-1])
          $usage .= ", ";
      }
    }
    $usage .= ")";

    if($returntype){
      $usage .= " : $returntype";
    }

    $template_method_body = str_replace("{{methodtext}}", $summary, $template_method_body);
    $template_method_body = str_replace("{{methodparameters}}", $methodparameters, $template_method_body);
    $template_method_body = str_replace("{{methodreturntext}}", $methodreturntext, $template_method_body);
    $template_method_body = str_replace("{{classname}}", $classname_without_namespace, $template_method_body);
    $template_method_body = str_replace("{{methodname}}", $method->getName(), $template_method_body);
    $template_method_body = str_replace("{{methodreturns}}", $returntype, $template_method_body);
    $template_method_body = str_replace("{{methodusage}}", $usage, $template_method_body);

    $template_method_head = str_replace("{{methodtext}}", $summary, $template_method_head);
    $template_method_head = str_replace("{{methodparameters}}", $methodparameters, $template_method_head);
    $template_method_head = str_replace("{{methodreturntext}}", $methodreturntext, $template_method_head);
    $template_method_head = str_replace("{{classname}}", $classname_without_namespace, $template_method_head);
    $template_method_head = str_replace("{{methodname}}", $method->getName(), $template_method_head);
    $template_method_head = str_replace("{{methodreturns}}", $returntype, $template_method_head);
    $template_method_head = str_replace("{{methodusage}}", $usage, $template_method_head);

    $template_method_whole = $template_method_head.$template_method_body;

    $all_methods_text .= $template_method_body;
    // write generated text into file
    echo $template_method_body;
    file_put_contents(__DIR__ . "/www/build/".VERSION."/methods/".$classname_without_namespace."-".$method->getName().".md", $template_method_whole);


  }


  // @ToDo
  $template_class = file_get_contents(__DIR__ . "/templates/class.template.html");
  $template_class = str_replace("{{classname}}", $classname, $template_class);
  $template_class = str_replace("{{classtext}}", $classsummary, $template_class);
  $template_class = str_replace("{{classexample}}", $classexample[0] ?? "", $template_class);


  $template_class = str_replace("{{classmethods}}", $all_methods_text, $template_class);

  file_put_contents(__DIR__ . "/www/build/".VERSION."/classes/".$classname_without_namespace.".md", $template_class);

  unlink(__DIR__ . "/www/build/latest");
  chdir(__DIR__ . "/www/build/");
  symlink(VERSION, "latest");

  foreach ($include_docs as $include_doc) {

    $s = $include_doc["src"]; //source
    $d = $include_doc["dst"]; //destination

    if(is_dir($s)){
      recurse_copy($s, $d);
    }else{
      copy($s, $d);
    }

  }

}

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
