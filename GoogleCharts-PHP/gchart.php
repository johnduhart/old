<?php

/*
 * GoogleChart - Creates charts using Google's API
 */
class GoogleChart {

    // Set the URL where the chart API is
    private $api_url = "http://chart.apis.google.com/chart?";

    private $chart_size = array('h' => 0, 'w' => 0);
    private $chart_data = array();
    private $chart_type = "";
    private $data_range = array('min' => 0, 'max' => 100);
    private $data_labels = array();
    private $chart_colors = array();
    private $chart_title = "";
    private $bar_graph_auto_size = false;
    private $pie_chart_orientation = "";
    private $venn_chart_data = array();
    private $scatter_point_data = array();

    private $debug = false;

    // Validation stuff
    private $chart_types = 
        array ('lc','ls','lxy','bhs','bvs','bhg','bvg','p','p3','pc','v','s','r','rs','t','gom','qr');

    function setDebug($set) {
        $this->debug = $set;
        $this->dmsg("Debugging on");
    }

    private function dmsg($msg, $fail=false, $pre="", $post="\n", $array=array()) {
        if($this->debug) {
            echo $pre."!!DEBUG!! ".$msg.$post;
            if(!empty($array))
                print_r($array);
            if($fail)
                die;
        }
    }

    function setType($code) {
        if ( in_array($code, $this->chart_types) ) {
            $this->chart_type = $code;
            $this->dmsg("Chart type set to ".$code);
        } else {
            $this->dmsg("setType failed. Type was ".$code, 1);
        }
    }

    function setSize($h,$w) {
        if( is_int($h) && is_int($w) ) {
            $this->chart_size['h'] = $h;
            $this->chart_size['w'] = $w;
            $this->dmsg("Chart size set for ".$h."h x ".$w."w");
        } else {
            $this->dmsg("setSize failed. Sizes were ".$h."h x ".$w."w", 1);
        }
    }

    function setRange($min,$max) {
        if( is_int($max) && is_int($min) ) {
            $this->data_range['min'] = $min;
            $this->data_range['max'] = $max;
            $this->dmsg("Data range set. Min is ".$min." and Max is ".$max);
        } else {
            $this->dmsg("setRange failed. Min was ".$min." and Max was ".$max, 1);
        }
    }

    function setChartColor($color) {
        $this->chart_colors[] = $color;
        $this->dmsg("Chart color set to ".$color);
    }

    function setChartColors($colors) {
        $this->chart_colors = array_merge($this->chart_colors, $colors);
        $this->dmsg("Charts colors set.",$array=$colors);
    }

    function setTitle($title) {
        $this->chart_title = $title;
        $this->dmsg("Title set to ".$title);
    }

    function setBarGraphAutoSize($set) {
        $this->bar_graph_auto_size = true;
        $this->dmsg("Bar graph auto sizing set to ".$set);
    }

    function setPieChartOrientation($radian) {
        $this->pie_chart_orientation = $radian;
        $this->dmsg("Pie chart orientation set to ".$radian);
    }

    function addData($data, $set=0) {
        if( ((int) $data) == $data ) {
            $this->chart_data[$set][] = $data;
            $this->dmsg("Data added. Data is ".$data." on set ".$set);
        } else {
            $this->dmsg("addData failed. Data was ".$data." on set ".$set, 1);
        }
    }

    function addDatas($datas, $set=0) {
        foreach($datas as $data) {
            if( ((int) $data) == $data ) {
                $this->chart_data[$set][] = $data;
                $this->dmsg("Datas added. Data is ".$data." on set ".$set);
            } else {
                $this->dmsg("addDatas subroutine failed. Data was ".$data." for set ".$set, 1);
            }
        }
    }

    function addVennData ($c1, $c2, $c3, $i12, $i13, $i23, $i123) {
        $this->venn_chart_data = array ($c1, $c2, $c3, $i12, $i13, $i23, $i123);
        $this->dmsg("Added ven data.");

    }

    function addScatterPoint ($x, $y, $size=100) {
        $this->scatter_point_data['x'][] = $x;
        $this->scatter_point_data['y'][] = $y;
        $this->scatter_point_data['s'][] = $size;
        $this->dmsg("Added scatter point data. X: ".$x." Y: ".$y." Size:".$size);
    }

    function addLabel($label, $axis) {
        if ($this->chart_type != "p" && $this->chart_type != "p3" && $this->chart_type != "gom") {
            $this->data_labels[$axis][] = $label;
            $this->dmsg("Label added. Label is ".$label." on axis ".$axis);
        } else {
            $this->data_labels[] = $label;
            $this->dmsg("Label added. Label is ".$label);
        }
    }

    function addLabels($labels, $axis) {
        if ($this->chart_type != "p" && $this->chart_type != "p3" && $this->chart_type != "gom") {
            foreach ($labels as $label) {
                $this->data_labels[$axis][] = $label;
                $this->dmsg("Label added. Label is ".$label." for axis ".$axis);
            }
        } else {
            foreach ($labels as $label) {
                $this->data_labels[] = $label;
                $this->dmsg("Label added. Label is ".$label);
            }
        }
    }

    function clean($data, $labels=false, $type=false, $range=false, $size=false, $colors=false, $title=false) {
        $this->dmsg("Clean invoked");
        if ($size)
            $this->chart_size = array('h' => 0, 'w' => 0);
        if ($data) {
            $this->chart_data = array();
            $this->venn_chart_data = array();
            $this->scatter_point_data = array(); }
        if ($type)
            $this->chart_type = "";
        if ($range)
            $this->data_range = array('min' => 0, 'max' => 100);
        if ($labels)
            $this->data_labels = array();
        if ($colors)
            $this->chart_colors = array();
        if ($title)
            $this->chart_title = "";
    }

    function buildUrl() {
        $this->dmsg("Starting buildUrl");
        $gUrl = "";
        $gUrl .= $this->api_url;
        $gUrl .= "chs=".$this->chart_size['w']."x".$this->chart_size['h'];
        $gUrl .= "&cht=".$this->chart_type;
        //$data_tag = "&chd=";
        $data_tag = "&chd=t:";
        // See below
        switch($this->chart_type) {
            case "v":
            {
                foreach ($this->venn_chart_data as $data) {
                    $data_tag.=$data.",";
                }
                $data_tag = substr($data_tag, 0, -1);
            }
            break;
            case "s":
            {
                $xSet = "";
                $ySet = "";
                $sSet = "";
                $i = 0;
                while(!empty($this->scatter_point_data['x'][$i]) && !empty($this->scatter_point_data['y'][$i])) {
                    $xSet .= $this->scatter_point_data['x'][$i].",";
                    $ySet .= $this->scatter_point_data['y'][$i].",";
                    $sSet .= $this->scatter_point_data['s'][$i].",";
                    $i++;
                }
                $xSet = substr($xSet, 0, -1);
                $ySet = substr($ySet, 0, -1);
                $sSet = substr($sSet, 0, -1);
                $data_tag.=$xSet."|".$ySet."|".$sSet;
            }
            break;
            case "gom":
            {
                $data_tag.=$this->chart_data[0][0];
            }
            break;
            default:
            {
                foreach ($this->chart_data as $set => $datas) {
                    // @todo: This causes failure. why did I do this?
                    //$data_tag .= "t".$set.":";
                    // Bar graphs dont like this ethier
                    //$data_tag .= "t:";
                    foreach ($datas as $data) {
                        $data_tag.=$data.",";
                    }
                    $data_tag = substr($data_tag, 0, -1);
                    $data_tag.="|";
                }
                $data_tag = substr($data_tag, 0, -1);
            }
            break;
        }
        $gUrl .= $data_tag;
        if (substr($this->chart_type, 0, 1) != "p") {
            $gUrl .= "&chds=".$this->data_range['min'].",".$this->data_range['max'];
        }
        if ($this->chart_type != "v" && $this->chart_type != "gom" && !empty($this->data_labels)) {
            if ($this->chart_type != "p" && $this->chart_type != "p3" && $this->chart_type != "gom")
            {
                $this->dmsg("In label agrument builder.");
                $label_sets = "&chxt=";
                $labels_tag = "&chxl=";
                $label_set_id = 0;
                //print_r($this->data_labels);
                foreach ($this->data_labels as $set => $labels) {
                    $label_sets .= $set.",";
                    $labels_tag .= $label_set_id.":|";
                    $label_set_id++;
                    $this->dmsg("Labels array:");
                    //print_r($labels);
                    foreach ($labels as $label) {
                        $labels_tag .= $label."|";
                    }
                }
                $label_sets = substr($label_sets, 0, -1);
                $labels_tag = substr($labels_tag, 0, -1);
                $this->dmsg("Label set: ".$label_sets." Labels tag: ".$labels_tag);
                $gUrl .= $label_sets.$labels_tag;
            } else {
                $label_tag = "&chl=";
                foreach ($this->data_labels as $label) {
                    $label_tag .= $label."|";
                }
                $label_tag = substr($label_tag, 0, -1);
                $gUrl .= $label_tag;
            }
        }

        if (!empty($this->chart_colors)) {
            $chart_color_tag = "&chco=";
            foreach ($this->chart_colors as $color) {
                $chart_color_tag.=$color.",";
            }
            $chart_color_tag = substr($chart_color_tag, 0, -1);
            $gUrl .= $chart_color_tag;
        }

        if (!empty($this->chart_title)) {
            $chart_title_tag = "&chtt=";
            $chart_title_tag.= str_replace(" ", "+", str_replace("\n", "|", $this->chart_title));
            $gUrl.=$chart_title_tag;
        }

        if ($this->bar_graph_auto_size && substr($this->chart_type, 0, 1) == "b") {
            $gUrl .= "&chbh=a";
        }

        if ($this->pie_chart_orientation != "" && substr($this->chart_type, 0, 1) == "p") {
            $gUrl .= "&chp=".$this->pie_chart_orientation;
        }

        return $gUrl;
    }

    function buildSafeUrl() {
        return str_replace("&", "&amp;", str_replace(" ", "%20", $this->buildUrl()));
    }


}