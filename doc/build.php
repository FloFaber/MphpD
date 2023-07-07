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

if(isset($argv[2])){
    define("LINK", !!$argv[2]);
}else{
    define("LINK", false);
}

define("VERSION", $argv[1]);


const CONFIG = [
  "source" => __DIR__ . "/../src/"
];


$docparser = new docPHParser(CONFIG);
$pd = new Parsedown();
$factory  = DocBlockFactory::createInstance();



rrmdir(__DIR__ . "/../docs/".VERSION);

/*// rebuild www dir


mkdir(__DIR__ . "/www/");
mkdir(__DIR__ . "/www/build/");*/
mkdir(__DIR__ . "/../docs/".VERSION."/");
mkdir(__DIR__ . "/../docs/".VERSION."/methods/");
mkdir(__DIR__ . "/../docs/".VERSION."/guides/");
mkdir(__DIR__ . "/../docs/".VERSION."/classes/");


// iterate through each class we found
$classes = [];
$methods = [];
foreach($docparser->getClasses() as $class){

  $class_info = [];

  $tmp = explode("\\", $class->getName());
  $class_info["name"] = array_pop($tmp); unset($tmp);
  $class_info["namespace"] = $class->getNamespaceName();
  $class_info["methods"] = $class->getMethods(Reflectionmethod::IS_PUBLIC);
  $class_info["docblock"] = $class->getDocComment() ? $factory->create($class->getDocComment()) : null;
  $class_info["summary"] = $class_info["docblock"]?->getSummary();
  $class_info["description"] = $class_info["docblock"]?->getDescription();
  $class_info["text"] = $pd->text($class_info["summary"]."\n".$class_info["description"]);
  $class_info["example"] = $class_info["docblock"]?->getTagsByName("example")[0];
  $class_info["template_file"] = __DIR__ . "/templates/class.template.html";

  $template_class = file_get_contents($class_info["template_file"]);
  $class_info["methods_text"] = ""; // holding filled templates of all methods in this class

  //continue;

  foreach($class_info["methods"] as $method){

    $method_info = [];

    $method_info["name"] = $method->getName();
    $method_info["class_info"] = $class_info;
    $method_info["params"] = $method->getParameters();
    $method_info["docblock"] = $method->getDocComment() ? $factory->create($method->getDocComment()) : null;
    $method_info["summary"] = $method_info["docblock"]?->getSummary();
    $method_info["description"] = $method_info["docblock"]?->getDescription();
    $method_info["text"] = $pd->text($method_info["summary"]."\n".$method_info["description"]);
    $method_info["template_file"] = __DIR__ . "/templates/method.template.html";
    $method_info["return_text"] = $pd->text($method_info["docblock"]?->getTagsByName("return")[0] ?? "");
    $method_info["return_type"] = $method->hasReturnType() ? $method->getReturnType()->getName() : "mixed";

    $template_method = file_get_contents($method_info["template_file"]);

    if($method_info["name"] === "__destruct"){ continue; }

    $method_info["params_text"] = "";
    // make parameter text
    /*foreach($method_info["params"] as $param){
      $method_info["params_text"] .= "<h5>\$".$param->getName()."</h5>";
    }*/
    foreach($method_info["docblock"]?->getTagsByName("param") as $param){
      $method_info["params_text"] .= $pd->text(($param)?->render());
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
        $usage .= " = ".($default === null ? "null" : $default);
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
    file_put_contents(__DIR__ . "/../docs/".VERSION."/methods/".$class_info["name"]."-".$method_info["name"].".html", $template_method);

    $methods[] = $method_info;

  }


  foreach($class_info as $k => $v){
    if(gettype($v) === "array"){ continue; }
    if(!str_contains($template_class, "{{class.$k}}")){ continue; }
    $template_class = str_replace("{{class.$k}}", $v, $template_class);
  }

  $template_page = file_get_contents(__DIR__ . "/templates/page.template.html");
  $template_page = str_replace("{{page.title}}", "MphpD - ".$class_info["name"], $template_page);
  $template_page = str_replace("{{page.content}}", $template_class, $template_page);
  $template_page = str_replace("{{page.version}}", VERSION, $template_page);
  file_put_contents(__DIR__ . "/../docs/".VERSION."/classes/".$class_info["name"].".html", $template_page);

  $classes[] = $class_info;

}


$template_page = file_get_contents(__DIR__ . "/templates/page.template.html");
$template_page = str_replace("{{page.title}}", "MphpD", $template_page);
$template_page = str_replace("{{page.content}}", $pd->text(file_get_contents(__DIR__ . "/../README.md")), $template_page);
$template_page = str_replace("{{page.version}}", VERSION, $template_page);

file_put_contents(__DIR__ . "/../docs/".VERSION."/index.html", $template_page);

$versions = [];
foreach(scandir(__DIR__ . "/../docs/") as $f){
  echo "$f\n";
    if(str_starts_with($f, "v") AND is_dir(__DIR__ . "/../docs/$f")){
        $versions[] = basename($f);
    }
}




$guides_text = "";
foreach(scandir(__DIR__ . "/guides/") as $f){
    if(is_dir(__DIR__ . "/guides/$f")){ continue; }
    $guide = file_get_contents(__DIR__ . "/guides/$f");
    $guide = $pd->text($guide);
    $dst = __DIR__ . "/../docs/".VERSION."/guides/".pathinfo($f, PATHINFO_FILENAME).".html";
    $guides_text .= "<li><a href='guides/".pathinfo($f, PATHINFO_FILENAME).".html'>".pathinfo($f, PATHINFO_FILENAME)."</a></li>\n";
    $tmp = file_get_contents(__DIR__ . "/templates/page.template.html");
    $tmp = str_replace("{{page.title}}", "MphpD Guides - $f", $tmp);
    $tmp = str_replace("{{page.content}}", $guide, $tmp);
    $tmp = str_replace("{{page.version}}", VERSION, $tmp);
    file_put_contents($dst, $tmp); unset($tmp);
}

$classes_text = "";
foreach($classes as $class){
    $classes_text .= "<li><a href='classes/".$class["name"].".html'>".$class["name"]."</a></li>\n";
}

$methods_text = "";
foreach($methods as $method){
    $methods_text .= "<li>
<a href='classes/".$method["class_info"]["name"].".html#".$method["name"]."'>".$method["class_info"]["name"]."::".$method["name"]."</a>
".($method["summary"] ? (" - ".$pd->line($method["summary"])) : "") ."</li>\n";
}


// update symlinks
var_dump(LINK);
if(VERSION !== "test" AND LINK === true){
    unlink(__DIR__ . "/../docs/latest");
    chdir(__DIR__ . "/../docs/");
    symlink(VERSION, "latest");

    unlink(__DIR__ . "/../docs/index.html");
    chdir(__DIR__ . "/../docs");
    symlink("latest/index.html", "index.html");
}


//$versions_text = "<li><a href='/latest/overview.html'>latest (".readlink(__DIR__ . "/../docs/latest").")</a>";
$versions_text = "// AUTOGENERATED BY build.php!\nwindow.versions = [";
rsort($versions);
foreach($versions as $version){
    //$versions_text .= "<li><a href='/$version/overview.html'>$version</a></li>\n";
  $versions_text .= "\"".$version."\",";
}
$versions_text .= "];";


/*$template_versions = file_get_contents(__DIR__ . "/templates/versions.template.html");
$template_versions = str_replace("{{versions.versions_text}}", $versions_text, $template_versions);*/
file_put_contents(__DIR__ . "/../docs/js/versions.js", $versions_text);


$template_overview = file_get_contents(__DIR__ . "/templates/overview.template.html");
$template_overview = str_replace("{{overview.guides_text}}", $guides_text, $template_overview);
$template_overview = str_replace("{{overview.classes_text}}", $classes_text, $template_overview);
$template_overview = str_replace("{{overview.methods_text}}", $methods_text, $template_overview);

$template_page = file_get_contents(__DIR__ . "/templates/page.template.html");
$template_page = str_replace("{{page.title}}", "MphpD - Overview", $template_page);
$template_page = str_replace("{{page.content}}", $template_overview, $template_page);
$template_page = str_replace("{{page.version}}", VERSION, $template_page);

file_put_contents(__DIR__ . "/../docs/".VERSION."/overview.html", $template_page);


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
