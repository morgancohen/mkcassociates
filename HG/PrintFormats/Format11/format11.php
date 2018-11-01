
<?php include ('hgfunctions.php') ?>
<?php
$subnum = 0;
$sectionPics = "";
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

	if ($print_options->pageBreaksBetweenSections) { ?>
		<div class="screen-page" style="page-break-before: always;">
<?php } else { ?>
		<div class="screen-page">
<?php } ?>

<!--Header-->
<div class="section-header">

<?php
	if ((! empty($section->candyImage)) && ($print_options->secImageInHeader)) { ?>
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
?>
<?php
            //
            //  handle the styles and materials
            //

            // get actual number of non-excluded SMs
            $nSMTotal = 0;
            foreach ($section_answer_repeat->sm_answer_array as $sm_answer) {
                if (!$sm_answer->excluded)
                    $nSMTotal++;
            }

            $insertedSM = 0;
            $tmp = '';
            for ($y = 0; $y < sizeof($section->sm_array); $y++) {
                $oneSM = '';
                $sm = $sm_array[$section->sm_array[$y]];
                $sm_answer = $section_answer_repeat->sm_answer_array[$y];
                if ($sm_answer->excluded)
                    continue;
                $insertedSM++;
                $oneSM .= "<div class=\"sm-whole\">\r";
                $oneSM .= "<span class=\"sm-header\" >\r";
                $oneSM .= $sm->name;
                $oneSM .= ":\r<br />\r</span>";
                for ($z = 0; $z < sizeof($sm->option_array); $z++) {
                    if ($sm_answer->check_array[$z] != 0) {
                        $oneSM .= "\r<span class=\"sm-answer\">";
                        if ($sm_answer->alt_values[$z] != "")
							$oneSM .= $sm_answer->alt_values[$z];
						else
							$oneSM .= $sm->option_array[$z];
                        $oneSM .= "\r<br />\r</span>";
                    }
                }
                if (!empty($sm->extraInfoName)) {
                    if (!empty($sm_answer->item)) {
                        $oneSM .= "\r<span class=\"sm-answer\">";
                        $oneSM .= $sm->extraInfoName;
                        $oneSM .= " : ";
                        $oneSM .= $sm_answer->item;
                        $oneSM .= "\r<br />\r</span>";
                    }
                }
                $oneSM .= "\r</div>";
                $tmp .= "\r<td class=\"sm-col\" valign=\"top\">";
                $tmp .= $oneSM;
                $tmp .= "\r</td>";
                if ($insertedSM % 3 == 0) {
                    // make sure we're finished
                    if ($insertedSM < $nSMTotal)
                        $tmp .= "\r</tr>\r<tr>\r";
                }
            }
            if ($insertedSM > 0) {
                //now round out the table with proper amount of cells
                if ($insertedSM % 3 == 0) {
                    //it came out just right so no extra needed
                } else {
                    if ($insertedSM % 3 == 2) {
                        $tmp .= "\r<td>\r&nbsp;\r</td>";
                    } else {
                        if ($insertedSM % 3 == 1) {
                            $tmp .= "\r<td>\r&nbsp;\r</td>\r<td>\r&nbsp;\r</td>";
                        }
                    }
                }
                $ret = "\r<div class=\"sm-title\" >";
                $ret .= htmlspecialchars($print_options->smName);
                $ret .= "\r<br />\r";
                $ret .= "\r</div>\r";
                $ret .= "\r<table class=\"sm-table\" width=\"100%\">\r<tr>\r";
                $ret .= $tmp;
                $ret .= "\r</tr></table>";
                echo $ret;
            }

            //
            // handle all of the Inspection items
            //
            $maxII = sizeof($section->ii_array);
            $section_col_headers = getSectionsColHeaders($section);
            $displayQNumber = 0;
            $ret = "\r<div class=\"sm-title\" style=\"page-break-after: avoid\">";
            $ret .= htmlspecialchars($print_options->iiName);
            $ret .= "\r</div>\r";

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

                $s = getInspectionItemDisplayNumber($subnum, $displayQNumber, $section_answer_repeat->
                    repeat_number, sizeof($section_answer->answer_repeat_array));
                $displayQNumber++;

                $pictureIndex = 1;
                $pictureLabel = $s;
                $pictureLabel .= " ";
                $pictureLabel .= $print_options->mediaName;

                $ret .= "\r<table class=\"form\" style=\"width:100%\">\r";
                $ret .= "\r<tr>\r<td class=\"form-question-number\">\r<span class=\"question-number\">";
                $ret .= $s;
                $ret .= "\r</span>\r</td>\r";
                $ret .= "\r<td class=\"form-question\" style=\"width:100%\">\r<span class=\"question\">";
                $ret .= $ii->text;
                $ret .= "\r<br />\r</span>\r";
				
				if (getCheckedNames($section_col_headers, $ii_answer->check_array)) {
					$ret .= "<span class=\"comments-header\">";
					$ret .= htmlspecialchars($print_options->commentName);
					$ret .= "\r</span>";
					$ret .= "\r<span class=\"answer\">";
					$ret .= getCheckedNames($section_col_headers, $ii_answer->check_array);
					$ret .= "\r<br />\r</span>\r";
				}
				
				$ret .= "</td></tr>\r";

                for ($xx = 0; $xx < sizeof($ii_answer->item_array); $xx++) {
                    $item = $ii_answer->item_array[$xx];
                    $color = '';
                    $icons = '';
                    getSummaryPrintInfo($item, $color, $icons, $summary_array);
                    $commentText = $item->comment;
                    $picText = "";
                    if ($print_doc->picsInReport && sizeof($item->picture_array) > 0) {
						$forceCenter = 0;
                        $sectionPics .= getSectionPics($item->picture_array, $pictureLabel, $pictureIndex, $forceCenter);
                    }
                    if (!empty($commentText) || !empty($picText) || !empty($icons)) {
                        //we need to put something in
                        $ret .= "\r<tr>\r<td valign=\"top\">\r";
                        $ret .= $icons;
                        $ret .= "\r</td>\r<td valign=\"top\">";
                        if ($print_options->picturesAboveCommentText && !empty($picText)) {
                            $ret .= $picText;
                        }
                        $ret .= "\r<span class=\"comments\" ";
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
                        $ret .= "\r</span>";
                        if (!$print_options->picturesAboveCommentText && !empty($picText)) {
                            $ret .= $picText;
                        }
                        $ret .= "\r</td>\r</tr>\r";
                    }
                }
                $ret .= "</table>\r";
                echo $ret;
                $ret = "";
            }

			if (! empty($sectionPics)) {
				$ret .= "<div class=\"sm-title\" style=\"page-break-after: avoid\">";
            	$ret .= htmlspecialchars("Section Photos");
            	$ret .= "\r</div>\r";
				if ($print_options->centerPics) {
					$ret .= "<div style=\"text-align: center; width: 100%; margin-top:5px; \">";
				} else {
					$ret .= "<div style=\"text-align: left; margin-top:5px; \">";
				}
				$ret .= $sectionPics;
				$ret .= "<div style=\"clear: both;\"></div>\r\n";
				$ret .= "</div>\r\n";
				echo $ret;
			}
			$sectionPics = "";
?>
<?php 		if (! empty($section_answer_repeat->footer)) { ?>
<div class="footertext"><?php echo $section_answer_repeat->footer; ?></div>

<?php 		} ?>
</div>

<!-- end screen-page -->
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
