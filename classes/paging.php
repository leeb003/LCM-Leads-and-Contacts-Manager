<?php
/**
 *  Simple class to create pagination routine div
**/

class paging
{  
    private $reload;
    private $page;
    private $tpages;
    private $adjacents;
   
    // Construct
    function __construct($reload, $page, $tpages, $adjacents) {
        $this->_reload = $reload;
        $this->_page = $page;
        $this->_tpages = $tpages;
        $this->_adjacents = $adjacents;
    }

    // Get pagination div
    function getDiv () {
        $prevlabel = "&lsaquo; Prev";
        $nextlabel = "Next &rsaquo;";
        $output = "<div class='pagin'>\n";
        // previous
        if ($this->_page==1) {
            $output .= "<span>$prevlabel</span>\n";
        } elseif($this->_page==2) {
            $output .= "<a href='$this->_reload' class='prevLog'>$prevlabel</a>\n";
        } else {
            $output .= "<a href='{$this->_reload}&amp;pages=" . ($this->_page - 1) . "' class='prevLog'>" 
                     . $prevlabel . "</a>\n";
        }

        // first
        if ($this->_page > ($this->_adjacents + 1)) {
            $output .= "<a href=\"" . $this->_reload . "\" class=\"logPage\">1</a>\n";
        }
    
        // interval
        if ($this->_page > ($this->_adjacents + 2)) {
            $output .= "...\n";
        }
        
        // pages
        $pmin = ($this->_page > $this->_adjacents) ? ($this->_page - $this->_adjacents) : 1;
        $pmax = ($this->_page < ($this->_tpages - $this->_adjacents)) ? ($this->_page + $this->_adjacents) : $this->_tpages;
        for ($i=$pmin; $i<=$pmax; $i++) {
            if ($i==$this->_page) {
                $output .= "<span class=\"current\">" . $i . "</span>\n";
            } elseif ($i==1) {
                $output .= "<a href=\"" . $this->_reload . "\" class=\"logPage\">" . $i . "</a>\n";
            } else {
                $output .= "<a href=\"" . $this->_reload . "&amp;pages=" . $i . "\" class=\"logPage\">" . $i . "</a>\n";
            }
        }

        // interval
        if ($this->_page < ($this->_tpages - $this->_adjacents - 1)) {
            $output .= "...\n";
        }

        // last
        if ($this->_page < ($this->_tpages - $this->_adjacents)) {
            $output .= "<a href=\"" . $this->_reload . "&amp;pages=" . $this->_tpages 
                    . "\" class=\"logPage\">" . $this->_tpages . "</a>\n";
        }

        // next
        if ($this->_page < $this->_tpages) {
            $output .= "<a href=\"" . $this->_reload . "&amp;pages=" . ($this->_page + 1) 
                    . "\" class=\"nextLog\">" . $nextlabel . "</a>\n";
        } else {
            $output .= "<span>" . $nextlabel . "</span>\n";
        }

        $output .= "</div>";

        return $output;
    }
}
