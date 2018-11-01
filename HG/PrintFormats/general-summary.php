<?php include('hgfunctions.php') ?>
<?php
$subnum = 0;
$seqNum = 0;

foreach ($sc_array as $section_container) {
	foreach ($section_container->section_array as $section) {
	
		$section_col_headers = getSectionsColHeaders($section);
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
			
			$sectionCellData = "";
			$sectionHeader = "<!-- start section -->\r";
			$max = sizeof($section->ii_array);
			$needHeader = TRUE;				
			$displayQNumber = 0;
			for ($i = 0; $i < $max ; $i++) {
				$ii = $ii_array[$section->ii_array[$i]];
				$ii_answer = $section_answer_repeat->ii_answer_array[$i];
				if (isIIExcluded($section_col_headers, $ii_answer->check_array)) {
					if (! $template->renumberOnExclude)
						$displayQNumber++;
					else if ($ii_answer->reinsp_excluded)
						$displayQNumber++;	// don't renumber if only excluded via reinspection
					continue;
				}
				$iiNumber = getInspectionItemDisplayNumber($subnum, $displayQNumber, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array));
				$displayQNumber++;				
				$iiNumber = rtrim($iiNumber);
				
				if (isAnyItemizedIteminSummary($ii_answer, $summaryID, $section_col_headers)) {
					
					//one itemized item needs to be in the summary so lets go thru them all
					if ($needHeader) {
						$cellData = "";
						if (! empty($section->candyImage))	{					
							$cellData .= "<img class=\"summary-header-image\" src=\"";
							$cellData .= $section->candyImage;
							$cellData .= "\" />";
						}
						$label = "<span class=\"header-number\">";
						$label .= getSectionDisplayLabel($subnum, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array));												
						$label .= "</span>";

						$cellData .= $label;
						$cellData .= getSectionName($section_answer_repeat);						

						$sectionHeader .= "<div class=\"summary-header\" >";
						$sectionHeader .= $cellData;
						$sectionHeader .= "</div>";
						$needHeader = FALSE;
						
					} // end need header
					$iiTable = "<!-- inspection item table-->\r";
					$iiTable .= "<table style=\"margin-top:5px;\" class=\"summary-form\">\r<tr class=\"summary-form-question\">";
					
					$cellData = "";
					if (! $print_doc->summarySeqNumbering) {
						$cellData .= "<span class=\"summary-question-number\">";
						$cellData .= $iiNumber;
						$cellData .= "&nbsp;</span>";
					}
					$iiTable .= "<td class=\"summary-form-number\" width=6%>";
					$iiTable .= $cellData;
					$iiTable .= "</td>\r\n";
					
					$cellData =  "<span class=\"summary-question\">";
					$cellData .= $ii->text;
					$cellData .= "</span>";
					
					$iiTable .= "<td class=\"summary-form-question\">";
					$iiTable .= $cellData;
					$iiTable .= "</td>\r\n<td>&nbsp;</td></tr>\r\n";

					$pictureNumber = 1;
					$needCheckedNames = TRUE;
					$pictureLabel = $iiNumber;
					$pictureLabel .= " ";
					$pictureLabel .= $print_options->mediaName;
					
					for ($itemizedNdx = 0; $itemizedNdx < sizeof($ii_answer->item_array); $itemizedNdx++) {	
					
						$itemized = $ii_answer->item_array[$itemizedNdx];

						$pictmp = "";
						if ($print_doc->picsInSummary && (! $print_doc->summarySeqNumbering)) {
							//still need to add up the picts used even if that itemized item is not in summary
							//so the picture number for another itemized item will be correct
							if ($print_doc->hma) {
								$pictureNumber = 1;
								if (sizeof($ii_answer->item_array) > 1) {
									$sHMAPicLabel = $iiNumber;
									$sHMAPicLabel .= " (";
									$sHMAPicLabel .= $itemizedNdx+1;
									$sHMAPicLabel .= ") ";
									$sHMAPicLabel .= $print_options->mediaName;;
									$pictmp = buildHorzPictureTable($itemized->picture_array, $HMAPicLabel, $pictureNumber);
								} else {
									$pictmp = buildHorzPictureTable($itemized->picture_array, $pictureLabel, $pictureNumber);
								}
							} else {
								$pictmp = buildHorzPictureTable($itemized->picture_array, $pictureLabel, $pictureNumber);
							}
						}

						if (! isInSummary($itemized, $summaryID)) continue;

						$seqNum++;
												
						if ($print_doc->summarySeqNumbering) {
							$pictureNumber = 1;
							$pictureLabel = "Item ";
							$pictureLabel .= $seqNum;
							$pictureLabel .= " - ";
							$pictureLabel .= $print_options->mediaName;
							$pictmp = buildHorzPictureTable($itemized->picture_array, $pictureLabel, $pictureNumber);
						}

						$estimateOutput = "";
						if ($include_report_estimates) {
							$plain = $itemized->estimate;
							if (! empty($plain)) {
								$estimateOutput .= "<div style=\"page-break-before:avoid\" class=\"summary-estimates\">";
								$estimateOutput .= $plain;
								$estimateOutput .= "</div>";
							}
						}

						//we only really need to add this row for the "inspected"/checked.
						$checkedNameOutput = "";
						$sNames = getCheckedNames($section_col_headers, $ii_answer->check_array);
						if (!empty($sNames)) {
							$checkedNameOutput = "<span class=\"summary-answer\">"; 
							$checkedNameOutput .= $sNames;
							$checkedNameOutput .= "</span> ";
						}
												
						if ( !$print_doc->excludeSummaryColumnHeaders && $needCheckedNames) {
							$needCheckedNames = FALSE;
							$iiTable .= "<tr class=\"summary-form-answer\">";
							$iiTable .= "<td></td>\r\n";
							$iiTable .= "<td class=\"summary-form-answer\">";
							$iiTable .= $checkedNameOutput;
							$iiTable .= "</td>\r\n";
							$iiTable .= "<td class=\"summary-form-estimate\">";
							//$iiTable .= "&nbsp;";
							$iiTable .= "</td>\r\n";
							$iiTable .= "</tr>\r\n";
						}

						$iiTable .= "<tr>";
						$cellData = "";
						$color = ""; $icons = "";
						getSummaryPrintInfo($itemized, $color, $icons, $summary_array);
						if (! empty($icons) ) {
							$cellData = $icons;
						}
						if ($print_doc->summarySeqNumbering) {
							$cellData .= $seqNum;
							$iiTable .= "<td class=\"summary-question-number\">";
							$iiTable .= $cellData;
							$iiTable .= "</td>\r\n";
						} else {
							$iiTable .= "<td class=\"summary-form-comments\">";
							$iiTable .= $cellData;
							$iiTable .= "</td>\r\n";
						}
						$cellData = "<div class=\"summary-comments\" ";
						$cellData .= $color;
						$cellData .= " >";
						
												
						if (sizeof($ii_answer->item_array) > 1 && $print_options->addItemizedNumbers) { 
							//if there is more than one itemized item then we
							//prefix the item with the item number
							$tnum = "";
							if ($print_doc->hma) {
								$tnum .= $iiNumber;
								$tnum .= " (";
								$itemnumber = $itemizedNdx+1;
								$tnum .= $itemnumber;
								$tnum .= ") ";
							} else {
								$tnum .= "(";
								$itemnumber = $itemizedNdx+1;								
								$tnum .= $itemnumber;
								$tnum .= ") ";
							}
							$cellData .= $tnum;
						}
						if ($print_doc->picsInSummary && $print_options->picturesAboveCommentText) {
							if (! empty($pictmp) ) {
								$cellData .= $pictmp;
							}
						}

						$cellData .= $itemized->comment;
						
						if ( $print_doc->summaryEstimates && ! empty($estimateOutput) ) {
							$cellData .= $estimateOutput;
						} 

						if ($print_doc->picsInSummary && !$print_options->picturesAboveCommentText) {
							if (! empty($pictmp)) {
								$cellData .= "<br />\r\n";
								$cellData .= $pictmp;
							}
						}						
						
						$cellData .= "</div>\r";											
						
						$iiTable .= "<td colspan=2 class=\"summary-form-comments\">";
						$iiTable .= $cellData;
						$iiTable .= "</td>\r\n";
						$iiTable .= "</tr>\r\n";
										
						
					}
					$iiTable .= "</table>\r\n";
					$sectionCellData .= $iiTable;
				}
			}				
			if (! $needHeader) { //did we ever find anything
				$ret = $sectionHeader;
				$ret .= $sectionCellData;
				$ret .= "<br />";
				echo $ret;
			}
		}
	}
}
?>
