<?php include ('hgfunctions.php') ?>


<?php
$subnum = 0;
foreach ($sc_array as $section_container) {
    foreach ($section_container->section_array as $section) {
        $section_answer = $section->section_answer;
        if ($section_answer->excluded) {
            if (!$template->renumberOnExclude)
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
<div class="contentDiv">
<h2 class="heading2">
<a name="<?php echo getAnchorName($section_answer_repeat)?>" id="<?php echo getAnchorName($section_answer_repeat)?>"></a>
<?php if (!empty($section->candyImage)) { ?>
	<img class="header-image" src="<?php echo $section->candyImage; ?>" />
<?php } ?>
	<span class="header-number"><?php echo getSectionDisplayLabel($subnum, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array)); ?></span><?php echo getSectionName($section_answer_repeat); ?>
</h2>

<div class="contenttext">

<!--Intro Text-->
<?php if (!empty($section_answer_repeat->intro)) { ?><div class="introtext"><?php echo $section_answer_repeat->intro; ?></div><?php } ?>				

<!--Pictures In Report if Checked-->	
	<?php
		if ($print_doc->picsInReport && (sizeof($section_answer_repeat->picture_array) > 0)) {
			$na = 0;
			$forceCenter = 1;
			echo buildHorzPictureTable($section_answer_repeat->picture_array, "", $na, $forceCenter);
		}

// START DIV FOR WATERMARK IMAGE
echo "<div class=\"watermark\">";
// END WATERMARK DIV AFTER COMMENTS

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
                $oneSM .= "<div class=\"sm-whole\">";
                $oneSM .= "<span class=\"sm-header\" >";
                $oneSM .= $sm->name;
                $oneSM .= ":<br /></span>";
                for ($z = 0; $z < sizeof($sm->option_array); $z++) {
                    if ($sm_answer->check_array[$z] != 0) {
                        $oneSM .= "<span class=\"sm-answer\">";
                        if ($sm_answer->alt_values[$z] != "")
							$oneSM .= $sm_answer->alt_values[$z];
						else
							$oneSM .= $sm->option_array[$z];
                        $oneSM .= "<br /></span>";
                    }
                }
                if (!empty($sm->extraInfoName)) {
                    if (!empty($sm_answer->item)) {
                        $oneSM .= "<span class=\"sm-answer\">";
                        $oneSM .= $sm->extraInfoName;
                        $oneSM .= " : ";
                        $oneSM .= $sm_answer->item;
                        $oneSM .= "<br /></span>";
                    }
                }
                $oneSM .= "</div>";
                $tmp .= "\r<td class=\"sm-col\" valign=\"top\">";
                $tmp .= $oneSM;
                $tmp .= "</td>";
                if ($insertedSM % 3 == 0) {
                    // make sure we're finished
                    if ($insertedSM < $nSMTotal)
                        $tmp .= "\r\n</tr>\r\n<tr>\r\n";
                }
            }
            if ($insertedSM > 0) {
                //now round out the table with proper amount of cells
                if ($insertedSM % 3 == 0) {
                    //it came out just right so no extra needed
                } else {
                    if ($insertedSM % 3 == 2) {
                        $tmp .= "\r<td>&nbsp;</td>";
                    } else {
                        if ($insertedSM % 3 == 1) {
                            $tmp .= "\r<td>&nbsp;</td>\r<td>&nbsp;</td>";
                        }
                    }
                }
                $snm = "<div class=\"sm-title\" >";
                $snm .= htmlspecialchars($print_options->smName);
                $snm .= "\r\n";
                $snm .= "<br /></div>\r\n";
                $snm .= "<table class=\"sm-table\" width=\"100%\">\r\n<tr>\r\n";
                $snm .= $tmp;
                $snm .= "\r\n</tr>\r</table>";
                echo $snm;
            }
?>

<!-- Column Headers -->

<?php

            $section_col_headers = getSectionsColHeaders($section);
            $ch = '';
            $omg = 0;
            $iih = '';
            for ($i = 0; $i < sizeof($section_col_headers); $i++) {
                if (!$section_col_headers[$i]->excludeColumn) {
                    ; //excludes to its answered
                    if ($omg)
                        $ch .= ", ";
                    $omg = 1;
                    $ch .= $section_col_headers[$i]->shortName;
                    $ch .= "=&nbsp;";
                    $ch .= $section_col_headers[$i]->longName;
                    $iih .= "<td width=\"4%\" class=\"form-question\" style=\"text-align:center\"><strong>";
                    $iih .= $section_col_headers[$i]->shortName;
                    $iih .= "</strong></td>\r";
                }
            }
?>
<!--End-->

<!--Inspection Items-->
<table class="form" style="width:100%">
<thead class="form">
<tr class="ghead">
<td class="form-question">&nbsp;</td>
<td class="form-question">&nbsp;</td>
<?php echo $iih;?>
</tr>
</thead>
<tbody class="form">
<?php
            //
            // handle all of the Inspection items
            //

            $maxII = sizeof($section->ii_array);
            $displayQNumber = 0;
            //cycles through $ii_aray for each item
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

                //End
                //Calls function to generate "question-number"
                $s = getInspectionItemDisplayNumber($subnum, $displayQNumber, $section_answer_repeat->
                    repeat_number, sizeof($section_answer->answer_repeat_array));
                $displayQNumber++;
                //End

                //Generates Inspection Items and comments
                $iir .= "\r<tr>\r<td class=\"form-question-number\"><span class=\"question-number\">";
                $iir .= $s;
                $iir .= "</span></td>\r\n";
                $iir .= "<td class=\"form-question\"><span class=\"question\">";
                $iir .= $ii->text;
                $iir .= "</span></td>";

                for ($i = 0; $i < sizeof($section_col_headers); $i++) {
                    if (!$section_col_headers[$i]->excludeColumn) { //excludes to its answered
                        if ($ii_answer->check_array[$i])
                            $iir .= "\r<td class=\"form-answer form-answer-checked\"><span class=\"answer\"><strong>&bull;</strong</span></td>";
                        else
                            $iir .= "\r<td class=\"form-answer\"><span class=\"answer\">&nbsp;</span></td>";
                    }
                }

                $iir .= "\r</tr>\r\n";
            }
            echo $iir;
            unset($iir);
?>
</tbody>
<tfoot>
<tr class="ghead">
<td class="form-question">&nbsp;</td>
<td class="form-question">&nbsp;</td>
<?php echo $iih;?>
</tr>
</tfoot>
</table>
<!--End-->

<!--Column Header Key-->
<div class="form-key"><?php echo $ch; ?></div>
<br />
<!--End-->

<!--Comments and Pictures-->
<?php
            $displayQNumber = 0;
            $outputComments = 0;
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
                //Cycles through pictures and picture labels
                for ($xx = 0; $xx < sizeof($ii_answer->item_array); $xx++) {
                    $item = $ii_answer->item_array[$xx];
                    $color = '';
                    $icons = '';
                    getSummaryPrintInfo($item, $color, $icons, $summary_array);
                    $commentText = $item->comment;

                    $picText = '';
                    $doSinglePicThing = 0;
                    $numPics = sizeof($item->picture_array);
                    if ($numPics > 0) {
                    	$pic = $item->picture_array[0];
                    	$doSinglePicThing = (($numPics == 1) && ($pic->width <= 450) && ($print_doc->picsInReport));
						$doSinglePicThing = false;
                    }
					if ($print_doc->picsInReport && ($numPics > 0) && (! $doSinglePicThing)) {	
						$forceCenter = 0;
						$picText .= buildHorzPictureTable($item->picture_array, $pictureLabel, $pictureIndex, $forceCenter);
					}
                    if (! empty($commentText) || ! empty($picText) || ! empty($icons) || $doSinglePicThing) {
                        //we need to put something in
                        if (! $outputComments) {
                        	$outputComments = 1;
                        	$comm .= "<h3 class=\"hedd1\">";
				            $comm .= $print_options->commentName;
				            $comm .= "</h3>\r\n";
                        }
                        $comm .= "<div class=\"commentsBox\">";
                        if ($doSinglePicThing) {
                        	$comm .= "<div class=\"textim\">";
                        	$comm .= $icons;
                        	$comm .= "<span class=\"comments-number\">";
                        	$comm .= $s;
                        	$comm .= "</span><span class=\"comments-sub-number\" ";
                        	$comm .= $color;
							$comm .= ">";
	                        if (sizeof($ii_answer->item_array) > 1 && $print_options->addItemizeNumbers) {
	                            //if there is more than one itemized item then we
	                            //prefix the item with the item number
	                            $comm .= '(';
	                            $comm .= $xx + 1;
	                            $comm .= ') ';
	                        }
	                        $comm .= "&nbsp;";
	                        $comm .= "</span><span class=\"comments-text\" ";
	                        $comm .= $color;
	                        $comm .= ">";
	                        $comm .= $commentText;
	                        $comm .= "</span>";
	                        $comm .= "</div>\r\n";

//							$comm .= "<div class=\"textim1\"><img src=\"images/line1.jpg\"  alt=\"\" /></div>\r\n";
							$picText2 = getSinglePic($item->picture_array, $pictureLabel, $pictureIndex, $forceCenter);
	                    	$comm .= $picText2;
	                    	$comm .= "<div style=\"clear: both;\"></div>\r\n";
                        } else {
	                        if ($print_options->picturesAboveCommentText && !empty($picText)) {
	                            $comm .= $picText;
	                        }
	                        $comm .= "<div class=\"avoid-page-break-after\">";
							$comm .= $icons;
	                        $comm .= "<span class=\"comments-number\">";
	                        $comm .= $s;
							$comm .= "</span><span class=\"comments-sub-number\" ";
                        	$comm .= $color;
							$comm .= ">";
	                        if (sizeof($ii_answer->item_array) > 1 && $print_options->addItemizeNumbers) {
	                            //if there is more than one itemized item then we
	                            //prefix the item with the item number
	                            $comm .= '(';
	                            $comm .= $xx + 1;
	                            $comm .= ') ';
	                        }
	                        $comm .= "</span>\r<span class=\"comments-text\" ";
	                        if (! empty($color)) {
	                        	$comm .= $color;
	                        }
	                        $comm .= ">";
	                        $comm .= "&nbsp;";
	                        $comm .= $commentText;
	                        $comm .= "</span>";
							$comm .= "</div>";
	                        if (!$print_options->picturesAboveCommentText && !empty($picText)) {
	                        	//$comm .= "<br />";
	                            $comm .= $picText;
	                        }
	                    }
	                    $comm .= "</div>\r\n";
                    }
                    echo $comm;
                    unset($comm);
                }
            }
?>
<!--End-->



<?php
// END DIV FOR WATERMARK IMAGE
echo "</div>";
// END WATERMARK DIV 
?>


<!--Footer -->
<?php 	if (!empty($section_answer_repeat->footer)) {
			?><div class="footertext">
			<?php echo $section_answer_repeat->footer; ?>
			</div>
<?php 	} ?>
</div>
</div>
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
