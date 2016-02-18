<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Ben's DataMC plots</title>
<script src="jquery-1.11.3.min.js"></script>
</head>

<body>

<div id="plots"></div>

<?php

  // Used for debugging the php
  /*echo 'Current PHP version: ' . phpversion();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);*/

  function FindDirs($dir) {
    $directories = array();
    $foldercontent = scandir($dir);
    natsort($foldercontent);
    foreach ($foldercontent as $item) {
      // print("item: $item\n");
      if ("$item" === "." or "$item" === ".." or "$item" === ".git") continue;
      $absitem = "$dir/$item";
      if (is_dir($absitem)) {
        $directories[] = "$item";
        $subdirs = FindDirs($absitem);
        foreach ($subdirs as $subitem) {
          $directories[] = "$item/$subitem";
        }
      }
    }
    return $directories;
  }

  function FindImages($folder, $filters = array(), $excludes = array()) {
    $images = array();
    $files = scandir($folder);
    foreach ($files as $file) {
      if (substr($file, -4) === ".svg" or substr($file, -4) === ".png") {
        $skip = False;
        foreach ($filters as $filter) {
          if (!strpos("$folder/$file",$filter)) {
            $skip = True;
            break;
          }
        }
        foreach ($excludes as $exclude) {
          if (strpos("$folder/$file",$exclude)) {
            $skip = True;
            break;
          }
        }
        if ($skip) {continue;}
        $images[] = "$folder/$file";
      }
    }
    sort($images);
    return $images;
  }

  // Make file names array key, useful for plot comparison
  function FileNameArray($array, $removeIndicies = array(), $fixButtonFunction = "") {
    $newarray = array();
    foreach ($array as $i => $value) {
      foreach ($value as $plot) {
        $folders = explode("/", $plot);
        $filename = array_pop($folders);
        $filename = substr($filename, 0, strlen($filename) - 4);
        foreach ($removeIndicies as $removeIndex) {
          unset($folders[$removeIndex]);
        }
        $button = implode("/",$folders);
        $button = "$button/$filename";
        if ($fixButtonFunction !== "") {
          $button = $fixButtonFunction($button);
        }
        if (array_key_exists($button, $newarray)) {
          $newarray[$button][] = $plot;
        } else {
          $newarray[$button] = array($plot);
        }
      }
    }
    return $newarray;
  }

  // Way to have different display name to actual name
  function FixButton($button) {
    // Example usage:
    // $button = str_replace("geq2j","2ji", $button);
    // $button = str_replace("geq2b","2bi", $button);
    // $button = str_replace("geq4j","4ji", $button);
    // $button = str_replace("geq4b","4bi", $button);
    return $button;
  }

  $baseDir = "./";
  $directories = FindDirs($baseDir);
  // Strings go in arrays to selectively choose buttons
  $filter = array();
  $exclude = array();
  $categories = array();
  foreach ($directories as $dir) {
    // print("$dir\n");
    $images = findImages("$baseDir/$dir", $filter, $exclude);
    if (count($images) !== 0) {
      $button = FixButton($dir);
      $categories[$button] = $images;
    }
  }
  // Example usage of File Name Array:
  // $categories = FileNameArray($categories, array(0,1,2,3), 'FixButton');
?>

<script>
  function getUrlParameters(parameter){
    parArr = window.location.search.substring(1).split("&");

    for(var i = 0; i < parArr.length; i++){
      parr = parArr[i].split("=");
      if(parr[0] == parameter){
        return decodeURIComponent(parr[1]);
      }
    }
    return false;
  }

  function plotHTML(categories, index) {
    var thisplots = categories[index];
    var html = ""

    if (thisplots && thisplots.length >= 1) {

      // Make title
      html += "</br>";
      html += index;
      html += "</br>";

      for (var i = 0; i < thisplots.length; i += 1) {
        html += "<a href=\"";
        html += thisplots[i];
        html += "\"><img src=\"";
        html += thisplots[i];
        html += "\" height=\"275\" /></a>";
      }
    } else {
      html += "</br><big>No plots found in ";
      html += index;
      html += ":(</big></br>";
    }

    return html;

  }

  function sameRoot(folder1, folder2) {
    var root1 = folder1.substr(0,folder1.search("/[^/]*$"));
    var root2 = folder2.substr(0,folder2.search("/[^/]*$"));
    if (root1 === root2) {
      return true;
    } else {
      return false;
    }
  }

  categories = <?php echo json_encode($categories); ?>;

  jQuery(document).ready(function() {

    var buttons = "";
    var lastButton = "";
    var tablify = true;
    if (tablify) {
      buttons += "<table cellpadding=0 cellspacing=0><tr>";
    }
    Object.keys(categories).forEach(function(category) {
      if (!sameRoot(lastButton, category)) {
        if (tablify) {
          buttons += "</tr><tr>"
        } else {
          buttons += "</br>";
        }
      }
      if (tablify) {
        buttons += "<td>"
      }
      lastButton = category;
      buttons += "<input type='button' value='" + category + "'>";
      if (tablify) {
        buttons += "</td>"
      }
    });
    if (tablify) {
      buttons += "</tr></table>"
    }

    $("body").prepend(buttons);

    // Loop over buttons (input elements)
    $("input").each(function( index, element ) {
      // Add listener for clicking button
      $( element ).on('click', function(event) {
        // Change content
        $("#plots").empty().append(plotHTML(categories, this.value));
        // Change URL
        var newURL = window.location.pathname + "?sel=" + this.value;
        history.replaceState({}, "", newURL);
        // Colour button text
        $("input").each(function( index, element ) {$(element).css("color", "")});
        $( this ).css("color", "Red");
      })
    });

    var param = getUrlParameters("sel");
    var index = Object.keys(categories)[0];
    if (param) {index = param;}
    $("#plots").empty().append(plotHTML(categories, index));
    // Colour button text
    $("input").each(function(thisIndex,element) {
      if (element.value === index) { $(element).css("color", "Red") }
    });

  });
</script>
</body>
</html>
