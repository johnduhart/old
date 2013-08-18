<?php

include_once "gchart.php";

$chart = new GoogleChart();
// We're going to debug
//$chart->setDebug(true);
// Type according to http://code.google.com/apis/chart/types.html
$chart->setType("gom");
// Size in H x W
$chart->setSize(200, 205);
// Chart data
$chart->addDatas(array(24,122,43,39,39), 0);
//$chart->addDatas(array(25,135,43,39,39), 1);
$chart->addVennData(100, 60, 80, 30, 25, 20, 10);
$chart->addScatterPoint(12, 14, 50);
// Labels, x is the axis
//$chart->addLabel("foo", "y");
//$chart->addLabel("bar", "y");
//  $chart->addLabels(array("not", "alot"), "x");
// Main chart color
$chart->setChartColors(array("2266FF", "4499FF"));
// Range for the chart
$chart->setRange(0, 150);
$chart->setTitle("ASDFSAG");
$chart->setBarGraphAutoSize(true);
// Return the built URL
?>
<html>
    <head />
    <body>
        <img src="<?php echo $chart->buildSafeUrl(); ?>" />
    </body>
</html>

