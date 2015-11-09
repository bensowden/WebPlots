<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Ben's DataMC plots</title>
<script src="jquery-1.11.3.min.js"></script>
</head>

<body>

<div id="plots"></div>

<?php
  function FindDirs($dir) {
    $directories = array();
    $foldercontent = scandir($dir);
    sort($foldercontent, SORT_NATURAL);
    foreach ($foldercontent as $item) {
      // print("item: $item\n");
      if ("$item" === "." or "$item" === "..") continue;
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

  function findImages($folder) {
    $images = array();
    $files = scandir($folder);
    foreach ($files as $file) {
      if (substr($file, -4) === ".svg" or substr($file, -4) === ".png") {
        $images[] = "$folder/$file";
      }
    }
    sort($images);
    return $images;
  }

  $directories = FindDirs("./");
  $categories = array();
  foreach ($directories as $dir) {
    // print("$dir\n");
    $images = findImages($dir);
    if (count($images) !== 0) {
      $categories[$dir] = $images;
    }
  }
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

  function tableRow(imga, imgb) {

    var row = "<tr>";
    row += "<td><img src=\"";
    row += imga;
    row += "\" height=\"325\" /></td>";
    row += "<td></td>";
    if (imgb !== "") {
      row +="<td><img src=\"";
      row += imgb;
      row += "\" height=\"325\" /></td>";
    }
    row += "</tr>";

    return row;
  }

  function makeTable(categories, index) {
    var thisplots = categories[index];
    var table = ""

    if (thisplots && thisplots.length >= 1) {

      // Make title
      table += "<table><tr>";
      table += "<td colspan=\"3\"><span class=\"tablaTitle\">";
      table += index;
      table += "</span></td></tr>";

      for (var i = 0; i < thisplots.length; i += 2) {
        if (i !== thisplots.length-1) table += tableRow(thisplots[i], thisplots[i+1]);
        else                          table += tableRow(thisplots[i], "" );
      }
      table += "</table>";
    } else {
      table += "</br><big>No plots found in ";
      table += index;
      table += ":(</big></br>";
    }

    return table;

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
        $("#plots").empty().append(makeTable(categories, this.value));

        var newURL = window.location.pathname + "?sel=" + this.value;
        history.replaceState({}, "", newURL);
      })
    });

    var param = getUrlParameters("sel");
    var index = Object.keys(categories)[0];
    if (param) {index = param;}
    $("#plots").empty().append(makeTable(categories, index));

  });
</script>
</body>
</html>

