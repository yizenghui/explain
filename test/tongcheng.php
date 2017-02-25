<?php
require '../vendor/autoload.php';

 use ExplainHtml\TongCheng;
// http://m.58.com/mm/yewu/27462874464970x.shtml
// http://m.58.com/mm/yewu/28311250063170x.shtml
$data = TongCheng::jobList('http://mm.58.com/yewu/');
print_r($data);

$data = TongCheng::jobInfo('http://m.58.com/mm/yewu/28311250063170x.shtml');
print_r($data);