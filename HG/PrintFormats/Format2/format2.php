<?php include ('hgfunctions.php') ?>

<?php
$subnum = 0;
foreach ($sc_array as $section_container) {
    foreach ($section_container->section_array as $section) {
        $section_answer = $section->section_answer;
        if ($section_answer->excluded) {
            if (! $template->renumberOnExclude)
                $subnum++;
            else if ($section_answer->reinsp_excluded)
				$subnum++;
            continue;
        }
        $subnum++;
        foreach ($section_answer->answer_repeat_array as $section_answer_repeat) {
?>

<!-- Column Headers -->
<?php
$section_col_headers = getSectionsColHeaders($section);
$ch = '';
$omg = 0;
$iih = '';
$num_col_headers = 0;
for ($i = 0; $i < sizeof($section_col_headers); $i++) {
    if (! $section_col_headers[$i]->excludeColumn) {
        if ($omg)
            $ch .= ", ";
        $omg = 1;
        $ch .= "<span class=\"form-key\">";
        $ch .= $section_col_headers[$i]->shortName;
        $ch .= "=&nbsp;";
        $ch .= $section_col_headers[$i]->longName;
        $ch .= "</span>";
    
        $iih .= "<td align=\"center\" width=\"4%\" class=\"abbrev\">";
        $iih .= $section_col_headers[$i]->shortName;
        $iih .= "</td>\r";
        $num_col_headers++;
    }
}

if ($print_options->pageBreaksBetweenSections) { ?>
		<div class="screen-page" style="page-break-before: always;">
<?php } else { ?>
		<div class="screen-page">
<?php } ?>

<!--Header-->
<div class="section-header">
<?php if ((! empty($section->candyImage)) && ($print_options->secImageInHeader)) { ?>
		<img class="header-image" src="<?php echo $section->candyImage; ?>"/>
<?php } ?>
	<a name="<?php echo getAnchorName($section_answer_repeat)?>" id="<?php echo getAnchorName($section_answer_repeat)?>"></a>
	<span class="header-number"><?php echo getSectionDisplayLabel($subnum, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array)); ?></span><?php echo getSectionName($section_answer_repeat); ?>
	<div style="clear: both;"></div>
</div>

<!--Intro Text-->
<div class="introtext">
<?php if ((! empty($section->candyImage)) && (! $print_options->secImageInHeader)) {  ?>
		<img class="header-image" src="<?php echo $section->candyImage; ?>"/>
<?php }
	  if (! empty($section_answer_repeat->intro)) {
		echo $section_answer_repeat->intro;
	 } ?>
</div>

<?php
		if ($print_doc->picsInReport && (sizeof($section_answer_repeat->picture_array) > 0)) {
			$na = 0;
			$forceCenter = 1;
			echo buildHorzPictureTable($section_answer_repeat->picture_array, "", $na, $forceCenter);
		}

	if ($num_col_headers > 0) { ?>
		<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr style="float:left">
		<td colspan="2" class="form-key-table" width="100%"><?php echo $ch; ?></td>
		</tr>
		</table>
		<table width="100%" >
		<tfoot class="form">
		<tr>
		<td colspan="2"><hr style="color:#000000" /></td>
		</tr>
		<tr>
		<td>
		<table align="center" width="100%">
		<tr>
		<?php echo $iih; ?>
		</tr>
		</table>
		</td>
		<td width="85%">
<?php } ?>
			<div class="abbrev"><?php echo(htmlspecialchars($print_options->iiName)); ?></div>
<?php if ($num_col_headers > 0) { ?>
		</td>
		</tr>
		<tr>
		<td colspan="2" class="form-key-table" width="100%"><?php echo $ch; ?></td>
		</tr>
		</tfoot>
		<thead>
		<tr>
		<td align="center">
		<table width="100%" style="text-align:center">
		<tr>
		<?php echo $iih; ?>
		</tr>
		</table>
		</td>
		<td width="85%"><div class="abbrev"><?php echo(htmlspecialchars($print_options->iiName)); ?></div></td>
		</tr>
		<tr>
		<td colspan="2"><hr style="color:#000000" /></td>
		</tr>
		</thead>   
<!--This is the Inspection Items-->
<?php } else { ?>
		<table width="100%" >
<?php }
        //
        // handle all of the Inspection items
        //

        $maxII = sizeof($section->ii_array);
        $section_col_headers = getSectionsColHeaders($section);
        $displayQNumber = 0;
        $ret = "";
        $num_ii_printed = 0;

        for ($x = 0; $x < $maxII; $x++) {
            $ii = $ii_array[$section->ii_array[$x]];
            if ($ii == null)
                continue;
            $ii_answer = $section_answer_repeat->ii_answer_array[$x];
            if (isIIExcluded($section_col_headers, $ii_answer->check_array)) {
                if (! $template->renumberOnExclude)
                    $displayQNumber++;
                else if ($ii_answer->reinsp_excluded)
					$displayQNumber++;
                continue;
            }
            $num_ii_printed++;

            $s = getInspectionItemDisplayNumber($subnum, $displayQNumber, $section_answer_repeat->
                repeat_number, sizeof($section_answer->answer_repeat_array));
            $displayQNumber++;

			$result = "";
			if ($num_col_headers > 0) {
	            $iir = "<tr>\r";
				//Answer Letter
	            for ($i = 0; $i < sizeof($section_col_headers); $i++) {
	                if (!$section_col_headers[$i]->excludeColumn) { 
	                    $iir .= "<td width=\"4%\" align=\"center\" ";
	                    if ($ii_answer->check_array[$i])
	                        $iir .= "class=\"form-answer-checked\"><span class=\"answer\"><strong>&bull;</strong></span></td>\r";
	                    else
	                        $iir .= "class=\"form-answer\"><span class=\"answer\">&nbsp;</span></td>\r";
	                }
	            }
	
	            $iir .= "</tr>\r";
	            $result = $iir;
	            unset($iir);
	        }

			$pictureIndex = 1;
            $pictureLabel = $s;
            $pictureLabel .= " ";
            $pictureLabel .= $print_options->mediaName;
            $ret .= "<tr class=\"form-ii-row\">\r";
            if ($num_col_headers > 0) {
				$ret .= "<td valign=\"top\" align=\"center\">\r";
				$ret .= "<table style=\"text-align:center\" width=\"100%\" class=\"form-checkboxes\">\r";
				$ret .= $result;
				$ret .= "</table>\r</td>\r";
			}
            $ret .= "<td style=\"width: 85%\" class=\"form-question\">";
			$ret .= "<table style=\"width: 100%\">\r<tr>\r<td class=\"question-number\">";
            $ret .= $s;
            $ret .= "</td>\r\n<td class=\"question\">";
            $ret .= $ii->text;
            $ret .= "</td></tr>\r";

//S&M
			$smPrinted = 0;
            for ($y = 0; $y < sizeof($section->sm_array); $y++) {
                $oneSM = '';
                $sm = $sm_array[$section->sm_array[$y]];
                $sm_answer = $section_answer_repeat->sm_answer_array[$y];
                if ($sm_answer->excluded)
                    continue;
                if ($num_ii_printed > 1) {
	                if ($sm->parentII != $ii->id) {
	                	continue;
	                }
	            } else {
	            	// first time thru, also get SMs with no parent
                	if (($sm->parentII != $ii->id) && ($sm->parentII != NULL)) {
                		continue;
                	}
	            }
				if ($smPrinted == 0) {
					$smPrinted = 1;
					$oneSM .= "<tr>\r<td>&nbsp;</td><td class=\"sm-cell\">";	
				}
				$oneSM .= "<span class=\"sm-header\" >";
                $oneSM .= $sm->name;
                $oneSM .= ":&nbsp;</span>\r";
                $afterFirst = 0;
                for ($z = 0; $z < sizeof($sm->option_array); $z++) {
                    if ($sm_answer->check_array[$z] != 0) {
                        $oneSM .= "<span class=\"sm-answer\">";
                        if ($afterFirst)
                        	$oneSM .= ", ";
						else
							$afterFirst = 1;
                        if ($sm_answer->alt_values[$z] != "")
							$oneSM .= $sm_answer->alt_values[$z];
						else
							$oneSM .= $sm->option_array[$z];
                        $oneSM .= "</span>";
                    }
                }
                $oneSM .= "<br />\r";
                if (! empty($sm->extraInfoName)) {
                    if (! empty($sm_answer->item)) {
                        $oneSM .= "<span class=\"sm-answer\">&nbsp;&nbsp;&nbsp;";
                        $oneSM .= $sm->extraInfoName;
                        $oneSM .= " : ";
                        $oneSM .= $sm_answer->item;
                        $oneSM .= "</span><br />";
                    }
                }

            	$ret .= $oneSM;
            }
            if ($smPrinted == 1) {
            	$ret .= "</td>\r\n</tr>";
            }

			$commentLabelPrinted=0;

            for ($xx = 0; $xx < sizeof($ii_answer->item_array); $xx++) {
                $item = $ii_answer->item_array[$xx];
				if (!empty($item->comment)) {					
					if ($commentLabelPrinted==0) {
						$commentLabelPrinted=1;
						$ret .= "<tr><td>&nbsp;</td>\r<td><span class=\"comments-header\">";
						$ret .= htmlspecialchars($print_options->commentName);
						$ret .= "</span>\r";
						$ret .= "</td>\r</tr>\r";
					}
				}
                $color = '';
                $icons = '';
                getSummaryPrintInfo($item, $color, $icons, $summary_array);
                $commentText = $item->comment;
                $picText = '';
                if ($print_doc->picsInReport && sizeof($item->picture_array) > 0) {
                	$forceCenter = 0;
                    $picText .= buildHorzPictureTable($item->picture_array, $pictureLabel, $pictureIndex, $forceCenter);
                }

                if (!empty($commentText) || !empty($picText) || !empty($icons)) {
                    //we need to put something in
                    $ret .= "<tr align=\"left\">\r<td>&nbsp;</td>";
                    $ret .= "\r<td class=\"form-comments\">";
					$ret .= "<div>";

                    if ($print_options->picturesAboveCommentText && !empty($picText)) {
                        $ret .= $picText;
                    }

					$ret .= $icons;
					
					$ret .= "\r<span class=\"comments";
						if ($print_options->picturesAboveCommentText) {
							$ret .= " comments-below"; 
						}
					$ret .= "\"";
					$ret .= $color;
					$ret .= " >";

                    if (sizeof($ii_answer->item_array) > 1 && $print_options->addItemizeNumbers) {
                        //if there is more than one itemized item then we
                        //prefix the item with the item number
                        $ret .= '(';
                        $ret .= $xx + 1;
                        $ret .= ') ';
                    }

                    $ret .= $commentText;
                    $ret .= "\r</span></div>";

                    if (!$print_options->picturesAboveCommentText && !empty($picText)) {
                        $ret .= $picText;
                    }
                    $ret .= "</td>\r</tr>\r";
                }
            }
            $ret .= "</table></td>\r</tr>\r";
        }

        echo $ret;

?>
</table>
</td>
</tr>
</table>
<?php if (!empty($section_answer_repeat->footer)) { ?>
	<div class="footertext"><?php echo $section_answer_repeat->footer; ?></div>
<?php } ?>
</div>

<?php
		$pbtoc = "<div class=\"screen-page-spacer\">\r\n<span><a href=\"#\">Top</a> ";
		if ($print_doc->includeTOC) {
			$tocName = $print_doc->tocName;
			$tocName = strip_tags($tocName);
			$tocName = preg_replace("/[^a-zA-Z0-9s]/", "", $tocName);
			$pbtoc .= "<a href=\"#";
			$pbtoc .= $tocName;
			$pbtoc .= "\">";
			$pbtoc .= $print_doc->tocName;
			$pbtoc .= "</a>\r\n";
		}
		$pbtoc .= "<a href=\"#end\">Bottom</a></span></div>\r\n";
		echo $pbtoc;
        } //end section repeat answers
    } //end section array
} // end section container array

?>