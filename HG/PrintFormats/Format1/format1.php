<?php include('hgfunctions.php') ?>


<?php 
	$subnum = 0;
	foreach ($sc_array as $section_container) {
		foreach ($section_container->section_array as $section) {
			$commentNamePrinted = FALSE;
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
		
<!--Pictures In Report if Checked-->	
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
		if (! $sm_answer->excluded)
			$nSMTotal++;	
	}

	$insertedSM = 0;
	$title = "<span class=\"sm-title\" >";
	$title .= htmlspecialchars($print_options->smName);
	$title .= "</span>\r<br />\r";
	$tmp = $title;
	for ($y = 0; $y < sizeof($section->sm_array); $y++) {
		$oneSM = '';
		$sm = $sm_array[$section->sm_array[$y]];
		$sm_answer = $section_answer_repeat->sm_answer_array[$y];
		if ($sm_answer->excluded) continue;		
		$insertedSM++;

		$oneSM .= "<span class=\"sm-header\" >";
		$oneSM .= $sm->name;
		$oneSM .= ":\r<br /></span>\r";		
		for ($z = 0; $z < sizeof($sm->option_array); $z++) {			
			if ($sm_answer->check_array[$z] != 0) { 
				$oneSM .= "<span class=\"sm-answer\">";
				if ($sm_answer->alt_values[$z] != "")
					$oneSM .= $sm_answer->alt_values[$z];
				else
					$oneSM .= $sm->option_array[$z];
				$oneSM .= "\r<br /></span>\r";
			}
		}
		if (! empty($sm->extraInfoName)) {
			if (! empty($sm_answer->item)) {
				$oneSM .= "<span class=\"sm-answer\">\r";
				$oneSM .= $sm->extraInfoName;
				$oneSM .= " : ";
				$oneSM .= $sm_answer->item;
				$oneSM .= "\r<br /></span>\r";
			}
		}
		$tmp .= $oneSM;
		if ($insertedSM % 3 == 0) {
			// make sure we're finished
			if ($insertedSM < $nSMTotal)
				$tmp .= "";
		}
	}
	if ($insertedSM > 0) {
		//now round out the table with proper amount of cells
		if ($insertedSM % 3 == 0) {
			//it came out just right so no extra needed
		} else {
			if ($insertedSM % 3 == 2) {
				$tmp .= "<span>&nbsp;</span>\r";
			} else {
				if ($insertedSM % 3 == 1) {
					$tmp .= "<span>&nbsp;</span>\r<span>&nbsp;</span>\r";
				}
			}
		}
		$snm = "<td valign=\"top\" style=\"text-align:left\" class=\"sm-table\">";
		$snm .= $tmp;
		$snm .= "</td>\r";
	} 
  
?>


<!-- Column Headers -->
<?php
	$section_col_headers = getSectionsColHeaders($section);
	$ch = '';
	$omg = 0;
	$iih = '';
for ($i = 0; $i < sizeof($section_col_headers); $i++) {
		if (!$section_col_headers[$i]->excludeColumn ) {; //excludes to its answered
		if ($omg)
			$ch .= ", ";
			$omg = 1;
			$ch .= $section_col_headers[$i]->shortName;
			$ch .= "=&nbsp;";
			$ch .= $section_col_headers[$i]->longName;
			$iih .= "<td width=\"4%\" style=\"text-align:center; font-size: 9pt;\"><strong>";
			$iih .= $section_col_headers[$i]->shortName;
			$iih .= "</strong></td>\r";
		}
	}	
?>
<!--Inspection Items-->

<table width="100%" cellpadding="5" cellspacing="0">
<tr>
<td style="vertical-align:top" width="80%">
<table class="form">
<thead class="form">
<tr>
<td colspan="2">&nbsp;</td>
<?php echo $iih; ?>
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
	for ($x = 0; $x < $maxII ; $x++) {
		$ii = $ii_array[$section->ii_array[$x]];
		if ($ii == null) continue;
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
		$s = getInspectionItemDisplayNumber($subnum, $displayQNumber, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array));
		$s = rtrim($s);
		$displayQNumber++;
	//End

	//Generates Inspection Items and comments
		$iir .= "\r<tr>\r<td style=\"text-align:center\" width=\"4%\" class=\"form-question-number\">\r<span class=\"question-number\">";
		$iir .= $s;
		$iir .= "</span>\r</td>\r\n";
		$iir .= "<td class=\"form-question\" ><span class=\"question\">";
		$iir .= $ii->text;
		$iir .= "</span></td>\r";
		
		for ($i = 0; $i < sizeof($section_col_headers); $i++) {
			if (!$section_col_headers[$i]->excludeColumn ) { //excludes to its answered
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
<tr>
<td class="form-key" colspan="2"><?php echo $ch;?></td>
<?php echo $iih;?>
</tr>
</tfoot>
</table>
</td>
<?php if ($insertedSM > 0) { echo $snm; }?>
</tr>
</table>
<!--End-->
<!--Column Header Key-->

<!--End-->
<!--Comments and Pictures-->
<div class="formhov">
<?php
		$displayQNumber = 0;
	for ($x = 0; $x < $maxII ; $x++){
		$ii = $ii_array[$section->ii_array[$x]];
		if ($ii == null) continue;
		$ii_answer = $section_answer_repeat->ii_answer_array[$x];
		if (isIIExcluded($section_col_headers, $ii_answer->check_array)) {
			if (! $template->renumberOnExclude)
				$displayQNumber++;
			else if ($ii_answer->reinsp_excluded)
				$displayQNumber++;
			continue;
		}
		$s = getInspectionItemDisplayNumber($subnum, $displayQNumber, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array));
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
			if ($print_doc->picsInReport && sizeof($item->picture_array) > 0) {	
				$forceCenter = 0;
				$picText .= buildHorzPictureTable($item->picture_array, $pictureLabel, $pictureIndex, $forceCenter);
			}

			if (! empty($commentText) || ! empty($picText) || ! empty($icons) ) {
				//we need to put something in
				$comm .= "<div class=\"comments-holder\" style=\"width: 100%\">";
				if ($print_options->picturesAboveCommentText && ! empty($picText)) {
					$comm .= $picText;
				}
				
				//updated-added this div with conditional class
				$comm .= "<div class=\"full-comments ";
				if ($print_options->picturesAboveCommentText) {
							$comm .= "full-comments-below";
						}
				$comm .= "\">";
				
				$comm .= $icons;
				if ($xx == 0) {
					$comm .= "<span class=\"comments-number\">";
					$comm .= $s;
					$comm .= "</span>";
				}
											
				if (sizeof($ii_answer->item_array) > 1 && $print_options->addItemizeNumbers) { 
				//if there is more than one itemized item then we
				//prefix the item with the item number
				$comm .= "<span class=\"comments\"";
				$comm .= $color;
				$comm .= ">(";
				$comm .= $xx+1;
				$comm .= ") </span>";
				}
				$comm .= "<span class=\"comments\"";
				$comm .= $color;
				$comm .= ">";
				$comm .= $commentText;
				$comm .= "</span>\r";
				$comm .= "</div>";
				if (! $print_options->picturesAboveCommentText && ! empty($picText)) {
					$comm .= $picText;
				}
				$comm .= "</div>\r";
			}
		if (!empty($comm)) {
			if (!$commentNamePrinted) {
				echo "<div class=\"comments-header\">". htmlspecialchars($print_options->commentName) ."</div>\r";
				$commentNamePrinted = TRUE;
			}
		}	
		echo $comm;
		unset($comm);			
		} // itemized items
	} // items
?>

<!--End-->
<!--Footer -->
<?php 	if (!empty($section_answer_repeat->footer)) {
			?><div class="footertext">
			<?php echo $section_answer_repeat->footer; ?>
			</div>
<?php 	} ?>
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
</div>
