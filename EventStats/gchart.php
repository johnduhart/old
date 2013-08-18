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

    private $debug = false;

    // Validation stuff
    private $chart_types = 
        array ('lc','ls','lxy','bhs','bvs','bhg','bvg','p','p3','pc','v','s','r','rs','t','gom','qr');

    function setDebug($set) {
        $this->debug = $set;
        $this->dmsg("Debugging on");
    }

    private function dmsg($msg, $fail=false, $pre="", $post="\n") {
        if($this->debug) {
            echo $pre."!!DEBUG!! ".$msg.$post;
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
        $this->dmsg("Charts colors set to "/*.print_r($colors)*/);
    }

    function setTitle($title) {
        $this->chart_title = $title;
        $this->dmsg("Title set to ".$title);
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
        if ($data)
            $this->chart_data = array();
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
        $data_tag = "&chd=";
        foreach ($this->chart_data as $set => $datas) {
            // @todo: This causes failure. why did I do this?
            //$data_tag .= "t".$set.":";
            $data_tag .= "t:";
            foreach ($datas as $data) {
                $data_tag.=$data.",";
            }
            $data_tag = substr($data_tag, 0, -1);
            $data_tag.="|";
        }
        $data_tag = substr($data_tag, 0, -1);
        $gUrl .= $data_tag;
        $gUrl .= "&chds=".$this->data_range['min'].",".$this->data_range['max'];
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

        return $gUrl;
    }

    function buildSafeUrl() {
        return str_replace("&", "&amp;", str_replace(" ", "%20", $this->buildUrl()));
    }


}