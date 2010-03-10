<?php
$dump_files = array('tera.sql','tera2.sql','tera3.sql','tera4.sql','tera5.sql');
foreach($dump_files as $file)
{
	$raw = file_get_contents($file);
	$content = gzcompress(&$raw,9);
	file_put_contents($file.'.gz', &$content);
}
