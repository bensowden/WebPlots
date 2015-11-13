# WebPlots

Website to dynamically find and display plots

# Introduction

WebPlots provides a simple way to display and interact with a gallery of .svg and/or .png images on a web server. It finds all .svg and/or .png images at the working and subdirectories and displays them with directory address buttons. The buttons can be used to navigate the directories of images and directories of images can be linked to directly using a link of a form like the following:

```
http://localhost/WebPlots/plots.php?sel=your/folder/here
```

# Setup

To use `plots.php`, ensure that [jquery-1.11.3.min.js](http://code.jquery.com/jquery-1.11.3.min.js) is at the working directory; `plots2.php` does not require this. To use `plots.php` or `plots2.php`, place a directory tree of .svg and/or .png images at the working directory.

# Credits

This package was created by [Ben Sowden](https://github.com/bensowden), plots2.php created by [Will Breaden Madden](https://github.com/wdbm).
