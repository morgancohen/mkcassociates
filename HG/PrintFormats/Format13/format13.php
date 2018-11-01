<?php include ('hgfunctions.php') ?>

<div class="screen-page" <?php if ($print_options->pageBreaksBetweenSections) { ?>style="page-break-before: always;"<?php } ?> >

<!--Title from items name in options -->
<?php
	echo "<div class=\"ii-main-title\">";
	echo htmlspecialchars($print_options->iiName);
	echo "</div>\r\n";
?>	

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
 ?>


<!--Inspection Items-->
<!--Comments and Pictures-->
<?php
            $comm = "";
			$displayTheHeader = 0;
			$firstInspectionItem = 0;
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

				$displayTheTitle = 0;
				$displayQNumber++;
                $pictureIndex = 1;
                //$pictureLabel = $s;
                //$pictureLabel .= " ";
                $pictureLabel = $print_options->mediaName;
				
				
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
?>						

<?php
// Header first time
				if ($displayTheHeader == 0) {
					$displayTheHeader++;
?>
					<!--Header-->
					<h2 class="heading2">
					<a name="<?php echo getAnchorName($section_answer_repeat)?>" id="<?php echo getAnchorName($section_answer_repeat)?>"></a>
					<?php if (!empty($section->candyImage)) { ?>
						<img class="header-image" src="<?php echo $section->candyImage; ?>" />
					<?php } ?>
						<span class="header-number"><?php echo getSectionDisplayLabel($subnum, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array)); ?></span><?php echo getSectionName($section_answer_repeat); ?>
					</h2>						
<?php 			} 
					
/* Moved to display only with content */
					
					if ($displayTheTitle == 0) {
						$displayTheTitle++;
						$comm .= "<div class=\"inspection-item";
						if ($firstInspectionItem == 0) {
							$comm .= " inspection-item-first";
							$firstInspectionItem++;
						}
						$comm .= "\">";
						$comm .= "<span class=\"comments-number\">";
						$comm .= $s;
						$comm .= "</span>";
						if ( getCheckedNames($section_col_headers, $ii_answer->check_array) ) {
							$comm .= "<span class=\"comments-header\">";
							$comm .= htmlspecialchars($print_options->commentName);
							$comm .= "</span>";
							$comm .= "\r<span class=\"answer\">";
							$comm .= getCheckedNames($section_col_headers, $ii_answer->check_array);
							$comm .= ":\r</span>\r";
						}
						//$comm .= "<span class=\"comments-number\">";
						//$comm .= $s;
						//$comm .= "</span><span class=\"question\">";
						$comm .= "<span class=\"question\">";
						$comm .= $ii->text;
						$comm .= "</span>";

						$comm .= "</div>\r";	
					}	
/*end of moved*/						
			
						
                        //we need to put something in
                        $comm .= "<div class=\"commentsBox\">";
                        if ($doSinglePicThing) {
                        	$comm .= "<div class=\"textim\">";
							$comm .= "</span><span class=\"comments-text\" ";
	                        $comm .= $color;
	                        $comm .= ">";
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
	                        //$comm .= "&nbsp;";
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
	                        $comm .=  "<span class=\"comments-text\" ";
	                        if (! empty($color)) {
	                        	$comm .= $color;
	                        }
	                        $comm .= ">";
							$comm .= $icons;
	                        if (sizeof($ii_answer->item_array) > 1 && $print_options->addItemizeNumbers) {
								$comm .= "<span class=\"comments-sub-number\" ";
								$comm .= $color;
								$comm .= ">";
	                            //if there is more than one itemized item then we
	                            //prefix the item with the item number
	                            $comm .= '(';
	                            $comm .= $xx + 1;
	                            $comm .= ') ';
								$comm .= "&nbsp;</span>\r";
	                        }
	                        
	                        //$comm .= "&nbsp;";
	                        $comm .= $commentText;
	                        $comm .= "</span>";
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


<!-- end screen-page -->
<?php
		} //end section repeat answers
	} //end section array
} // end section container array
?>
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
?>
















<div class="screen-page" <?php if ($print_options->pageBreaksBetweenSections) { ?>style="page-break-before: always;"<?php } ?> >


<!--Title from styles name in options -->
<?php
	echo "<div class=\"sm-main-title\">";
	echo htmlspecialchars($print_options->smName); 
	echo "</div>";
?>


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
            $comm = "";
			$displayTheHeader = 0;
            $displayQNumber = 0;
            $maxII = sizeof($section->ii_array);

			// Header first time
			?>
			<!--Header-->
			<h2 class="heading2">
			<a name="<?php echo getAnchorName($section_answer_repeat)?>" id="<?php echo getAnchorName($section_answer_repeat)?>"></a>
			<?php if (!empty($section->candyImage)) { ?>
				<img class="header-image" src="<?php echo $section->candyImage; ?>" />
			<?php } ?>
				<span class="header-number"><?php echo getSectionDisplayLabel($subnum, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array)); ?></span><?php echo getSectionName($section_answer_repeat); ?>
			</h2>						
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
			$insertSecondDiv = FALSE;
            $tmp = '';						
            
			for ($y = 0; $y < sizeof($section->sm_array); $y++) {
                $oneSM = '';
                $sm = $sm_array[$section->sm_array[$y]];
                $sm_answer = $section_answer_repeat->sm_answer_array[$y];
                if ($sm_answer->excluded)
                    continue;
                $insertedSM++;

                $oneSM .= "<span class=\"sm-header\" >";
                $oneSM .= $sm->name;
                $oneSM .= ": </span>";
                for ($z = 0; $z < sizeof($sm->option_array); $z++) {
                    if ($sm_answer->check_array[$z] != 0) {
                        $oneSM .= "<span class=\"sm-answer\">&bull;";
                        if ($sm_answer->alt_values[$z] != "")
							$oneSM .= $sm_answer->alt_values[$z];
						else
							$oneSM .= $sm->option_array[$z];
                        $oneSM .= "</span> ";
                    }
                }
                if (!empty($sm->extraInfoName)) {
                    if (!empty($sm_answer->item)) {
                        $oneSM .= "<span class=\"sm-answer\">&bull;";
						$oneSM .= "<span class=\"sm-extra-info\">";
                        $oneSM .= $sm->extraInfoName;
                        $oneSM .= ": ";
                        $oneSM .= $sm_answer->item;
						$oneSM .= "</span> ";
                        $oneSM .= "</span> ";
                    }
                }

			    //splits up sm's into 2 separate divs
				if ($insertedSM == 1) {
					$tmp .= "<div class=\"sm-container\">";
				}
				if ($insertSecondDiv == TRUE) {
					$tmp .= "<div class=\"sm-container\">";
					$insertSecondDiv = FALSE;
				} 
				
				$tmp .= "<div class=\"sm-whole\">";
                $tmp .= $oneSM;
				$tmp .= "</div>";

				if ( ($nSMTotal / 2 == $insertedSM) || ( ($nSMTotal+1) / 2 == $insertedSM) ) {
					$tmp .= "</div>";
					$insertSecondDiv = TRUE;
				}
				if ($nSMTotal == $insertedSM && $insertSecondDiv == FALSE) {
					$tmp .= "</div>";
				}
				
            }
            if ($insertedSM > 0) {
                
                // $snm .= htmlspecialchars($print_options->smName);
               
				$snm = "<div>";
				$snm .= $tmp;
				$snm .= "</div>";

				echo $snm;
				echo "<div style=\"clear:both\"></div>";
				
            }
			/* End of Styles/Materials */
            
			$itemsContainer = TRUE;
			$oneColHeaderList = "";
			$firstColumnHeader = TRUE;
			
			for ($sectionColHeaderIndex = 0; $sectionColHeaderIndex < sizeof($section_col_headers); $sectionColHeaderIndex++) {

				$displayColumnHeader = TRUE;	
				
				$section_col_header = $section_col_headers[$sectionColHeaderIndex];
				//echo $section_col_header->longName;
				
				for ($x = 0; $x < $maxII; $x++) {
					$ii = $ii_array[$section->ii_array[$x]];
					if ($ii == null)
						continue;
					$ii_answer = $section_answer_repeat->ii_answer_array[$x];
					if ($ii_answer==0) continue;
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
					$pictureLabel = $print_options->mediaName;
					
					
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
						
						
					/* Moved to display only with content */
					if ($itemsContainer==TRUE) {
						if ( empty($commentText) && empty($picText) && empty($icons) ) {
							if ( getCheckedNames($section_col_headers, $ii_answer->check_array) ) {
								echo "<div class=\"inspection-item-group\">";
								$itemsContainer=FALSE;
							}
						}
					}

					if ( empty($commentText) && empty($picText) && empty($icons) ) {
											
							$comm .= "<span class=\"inspection-item-empty\">";
							if ( getCheckedNames($section_col_headers, $ii_answer->check_array) ) {							
								//$comm .= "<span class=\"comments-header\">";
								//$comm .= htmlspecialchars($print_options->commentName);
								//$comm .= "</span>";
								if ( ($displayColumnHeader == TRUE) && ($section_col_header->longName == getCheckedNames($section_col_headers, $ii_answer->check_array)) ) {
									if ($firstColumnHeader == FALSE) {
										$comm .= "\r<br />";
									}
									$comm .= "\r<span class=\"answer\">";
									$comm .= getCheckedNames($section_col_headers, $ii_answer->check_array);
									$comm .= ":\r</span>\r";
									$displayColumnHeader = FALSE;
									$firstColumnHeader = FALSE;
								}
							}
							$comm .= "<span class=\"comments-number-empty\">";
							$comm .= $s;
							if ($section_col_header->longName == getCheckedNames($section_col_headers, $ii_answer->check_array)) {
								$comm .= "</span><span class=\"question\">&bull;";
								$comm .= $ii->text;
								$comm .= "</span>";
							}
							$comm .= "</span>\r";	

						/*end of moved*/						
				
						}
						echo $comm;
						unset($comm);
					}
					
				}
			}
			if ($itemsContainer==FALSE) {
				echo "</div>"; // End of inpection-item-group
			}
?>
<!--End-->


<!-- end screen-page -->
<?php
		} //end section repeat answers
	} //end section array
} // end section container array
?>
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
?>