<?php
/*
 * MphpD
 * http://mphpd.org
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

// thanks to https://github.com/phoboslab
require_once __DIR__ . "/inc/pagenode.php";

const FOLDERS = [
  "guides", "classes", "methods"
];

route("/doc/{version}/doc", function($version){
  include(__DIR__ . "/templates/header.html.php");

  echo "<details>\n<summary><h3 style='display: inline-block; cursor: pointer;'>Versions</h3></summary>\n<ul>\n";
  foreach(scandir("build/") as $v){
    if($v === "." || $v === ".."){ continue; }
    echo "<li><a href='/doc/$v/doc'>".($v === $version ? "<b>$v</b>" : $v)."</a></li>";
  }
  echo "</ul>\n</details>\n";

  foreach(FOLDERS as $folder){
    echo "<h1 style='text-transform: capitalize;'>$folder</h1>\n";
    $nodes = select("build/$version/$folder")->query("title", "asc", 0, [], false);
    if(!$nodes){ return false; }
    echo "<ul>\n";
    foreach($nodes as $node){
      echo "<li><a href='/doc/$version/$folder/$node->keyword'>".$node->title."</a></li>\n";
    }
    echo "</ul>\n";
  }
  include(__DIR__ . "/templates/footer.html.php");
});

route("/doc/{version}/{page}", function($version, $page){
  $node = select("build/$version/")->one([ "keyword" => $page ]);
  if(!$node){ return false; }
  include __DIR__ . "/templates/page.html.php";
});

route("/doc/{version}/{folder}/{page}", function($version, $folder, $page){
  $node = select("build/$version/$folder")->one([ "keyword" => $page ]);
  if(!$node){ return false; }
  include __DIR__ . "/templates/page.html.php";
});

reroute("/", "/doc/latest/index");
reroute("/doc", "/doc/latest/doc");

route("/*", function(){
  include __DIR__ . "/templates/404.html.php";
});

dispatch();
