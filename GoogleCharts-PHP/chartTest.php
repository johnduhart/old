<?php

include_once "gchart.php";

$chart = new GoogleChart();

$chart->setType("p");
$chart->setSize(100, 200);
$chart->addData(12);
$chart->addData(123);
$chart->addData("12");

$chart->addLabels(array('A','B','C'), null);
echo $chart->buildUrl();