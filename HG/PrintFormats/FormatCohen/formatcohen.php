<?php include ('hgfunctions.php') ?>

<?php
$subnum = 0;
foreach ($sc_array as $section_container) {
    foreach ($section_container->section_array as $section) {
    	$section_col_headers = getSectionsColHeaders($section);
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

<?php if ($subnum == 1) { ?>
	<h1 class="BodyHeader">Main Body of Report</h1>
<?php } ?>

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
		
	?>	

<!--Begin Watermark-->
<div class="watermark">
	
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


<!--Inspection Items-->
<!--Comments and Pictures-->
<?php
            $comm = "<div class=\"sm-title\" >";
            $comm .= htmlspecialchars($print_options->iiName);
            $comm .= "\r\n";
            $comm .= "<br /></div>\r\n";
            $displayQNumber = 0;
            $maxII = sizeof($section->ii_array);
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
                $comm .= "<div style=\"margin-top: 10px;\">";
                $comm .= "<span class=\"comments-number\">";
                $comm .= $s;
                $comm .= "</span><span class=\"question\">";
                $comm .= $ii->text;
                $comm .= "</span><br />";
                $comm .= "<span class=\"comments-header\">";
                $comm .= htmlspecialchars($print_options->commentName);
                $comm .= "</span>&nbsp;";
                $comm .= "\r<span class=\"answer\">";
                $comm .= getCheckedNames($section_col_headers, $ii_answer->check_array);
                $comm .= "\r<br />\r</span>\r</div>\r";
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
                    	$doSinglePicThing = (($numPics == 1) && ($pic->width <= 450));
						$doSinglePicThing = false;
                    }
					if ($print_doc->picsInReport && ($numPics > 0) && (! $doSinglePicThing)) {	
						$forceCenter = 0;
						$picText .= buildHorzPictureTable($item->picture_array, $pictureLabel, $pictureIndex, $forceCenter);
					}

                    if (! empty($commentText) || ! empty($picText) || ! empty($icons) || $doSinglePicThing) {
                        //we need to put something in
                        $comm .= "<div class=\"commentsBox\">";
                        if ($doSinglePicThing) {
                        	$comm .= "<div class=\"textim\">";
                        	$comm .= $icons;
                        	$comm .= "<span class=\"comments-sub-number\" ";
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
	                        $comm .= $icons;
	                        $comm .= "<span class=\"comments-sub-number\" ";
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
	                        if (!$print_options->picturesAboveCommentText && !empty($picText)) {
	                        	$comm .= "<br />";
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


<!--End Watermark-->
</div>


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
