<?php

/**

latentsee.php 0.1 (http://latentsee.com/)

Web based HTTP performance visualizer

LatentSee downloads a series of differently sized files and plots
the retrieval times. It can also be used to generate a single file
of a specified bytesize.

Be sure to compression is disabled on the webserver.
LatentSee is currently only being tested with Firefox on Linux/OSX. YMMV.

  GET /latentsee.php           #=> plot request times for a series of files
  GET /latentsee.php?bytes=100 #=> returns file 100 bytes in length

* Play with demo version: http://latentsee.com
* Download: http://github.com/mbailey/latentsee
* Share and discuss results on Facebook: http://bit.ly/c4WUKv

* Copyright (c) 2010 Mike Bailey <mike@bailey.net.au>

* The MIT License http://creativecommons.org/licenses/MIT/

**/

// If the number of bytes to return is specified
if ($bytes = (int)$_REQUEST['bytes']){

  // Return file of size $bytes
  send_bytes($bytes);

} else {

  // Show HTML page that runs test and plots results
  display_form();

}

// Functions

function send_bytes($bytes) {
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  if (! $_REQUEST['keepalive']) header("Connection: close") ;
  $header = "$bytes bytes\n";
  print $header;
  print str_repeat('x', $bytes - strlen($header));
}

function display_form() {

  $seq= "var seq= '" . ($_REQUEST['seq'] ? $_REQUEST['seq'] : '9x1|9x10') . "';";
  $keepalive = $_REQUEST['keepalive'] ? "'&keepalive=true'" : "''";

  print <<<EOH
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
<title>LatentSee - Web Latency Visualizer</title>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/redmond/jquery-ui.css" type="text/css" />
<style>
BODY { 
    font-family: "Trebuchet MS", "Bitstream Vera Serif", Utopia, "Times New Roman", times, serif;
}
h1 {text-align: center; display:block; margin-left: auto; width: 600px; margin-right: auto; color: blue;}
div#info
{
        margin-left: auto;
        margin-right: auto;
        width: 600px;
        background-color: lightgrey;
}

div#content
{
        width: 600px;
        margin-left: auto;
        margin-right: auto;
}

</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js"></script>
</head>

<body>

<h1>LatentSee - Web Latency Visualizer</h1>

<div id="info">
<p>This page is requesting files from the server and timing how long it takes to get them. You should see a chart quite soon!</p>

<p>If your browser gets impatient please tell it to wait.</p>
</div>

<div id="content">

</div>

<script>
$seq
var gc_base_url = 'http://chart.apis.google.com/chart';
var gc_args = {
    chxt: 'x,x,y,y',
    chxl: '1:|Filesize (KB)|3:|Time (ms)',
    chxp: '1,50|3,50',
    chs: '650x450',
    cht: 's',
    chco: 'FF0000'
  }
var arr_filesizes = []; // Filesizes from tests
var arr_times = []; // Retrieval times for files
$.ajaxSetup({async: false, cache: false });
var seqs = [];
var total_files;

function run() {
  getFiles();
  prepareGcArgs();
  url = prepareURL();
  $('#content').html('<img src="' + url +'">');
  // document.write('<img src="' + url +'">');
}

// Functions

function getSeqs() {
  total_files = 0;
  $.each(seq.split('|'), function(junk, series) {
    count_interval = series.split('x');
    seqs.push(count_interval);
    total_files += parseFloat(count_interval[0]);
  });
}

function getFiles() {
  // XXX get total number of files for progressbar
  $('#content').html('<div id="progressbar"></div>');
  $(function() { $("#progressbar").progressbar({ value: 0 }); });
  getSeqs();
  progressbar_step = 100 / total_files;
  var cur_size = 0;
  $.each(seqs, function(junk, series) {
    count = series[0];
    increment = series[1];
    for (counter = 1; counter <= count; counter += 1) { 
      getFile(cur_size + counter * increment); 
      $("#progressbar").progressbar('value', $("#progressbar").progressbar('value') + progressbar_step);
    }
    cur_size += count * increment;
  });

}

function getFile(kb) {
  var start = new Date().getTime();
  $.get('?bytes=' + (kb * 1024) + $keepalive);
  var elapsed = new Date().getTime() - start;
  arr_times.push(elapsed);
  arr_filesizes.push(kb);
}

function prepareGcArgs() {
  gc_args['chd'] = 't:' + arr_filesizes.join(',') + '|' + arr_times.join(',');
  gc_args['chds'] = '0,'+ Array.max(arr_filesizes)+',0,'+ Array.max(arr_times);
  gc_args['chxr'] = '0,0,'+Array.max(arr_filesizes)+'|2,0,'+Array.max(arr_times);
  gc_args['chdl'] = self.location.hostname;
}

function prepareURL() {
  url = gc_base_url + '?';
  $.each(gc_args, function(key, val) { url = url + key + '=' + val + '&'; });
  return url;
}

Array.max = function( array ){
  return Math.max.apply( Math, array );
};

Array.min = function( array ){
  return Math.min.apply( Math, array );
};

$(document).ready(function() {
 run();
});
</script>

</body></html>
EOH;
}

?>
