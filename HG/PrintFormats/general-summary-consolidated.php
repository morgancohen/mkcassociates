<?php include('hgfunctions.php') ?>
<?php
$seqNum = 0;
$printed_summaries = getListOfPrintedSummaries($summary_array, $print_doc);
for ($sumNdx = 0; $sumNdx < sizeof($printed_summaries); $sumNdx++) {
	$needSummaryHeader = TRUE;
	$pSummary = $printed_summaries[$sumNdx];
	$summaryID = $pSummary->id;
	$subnum = 0;
	$bFirstHeader = TRUE;
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
				$needSectionHeader = FALSE;				

				$max = sizeof($section->ii_array);
				$displayQNumber = 0;
				for ($i = 0; $i < $max ; $i++) {
					$ii = $ii_array[$section->ii_array[$i]];
					if ($ii == null) continue;
					$ii_answer = $section_answer_repeat->ii_answer_array[$i];
					if (isIIExcluded($section_col_headers, $ii_answer->check_array)) {
						if (! $template->renumberOnExclude)
							$displayQNumber++;
						else if ($ii_answer->reinsp_excluded)
							$displayQNumber++;
						continue;
					}

					$iiNumber = getInspectionItemDisplayNumber($subnum, $displayQNumber, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array));
					$displayQNumber++;				
					$iiNumber = rtrim($iiNumber);
					
					$pictureLabel = $iiNumber;
					$pictureLabel .= " ";
					$pictureLabel .= $print_options->mediaName;

					if (isAnyItemizedIteminSummary($ii_answer, $summaryID, $section_col_headers)) {
						if ($needSummaryHeader) {
							$ret .= "<div class=\"summary-title-consolidated\">";
							if ($pSummary->useIcon && ! empty($pSummary->iconFilename))	{					
								$ret .= "<img class=\"summaryIcon\" src=\"";
								$ret .= $pSummary->iconFilename;
								$ret .= "\" />";
							}
							$ret .= $pSummary->name;
							$ret .= "</div>";
							$needSummaryHeader = FALSE;
						}

						if ($needSectionHeader) {
							if ($bFirstHeader)
								$bFirstHeader = FALSE;
							else
								$ret .= "<br />";
							$ret .= "<div class=\"summary-header\">";
							$label = "<span class=\"header-number\">";
							$label .= getSectionDisplayLabel($subnum, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array));												
							$label .= "</span>";
							$ret .= $label;
							$ret .= getSectionName($section_answer_repeat);													
							$ret .= "</div>";
							$needSectionHeader = FALSE;
						}

						$ret .= "<table style=\"width:100%\"><tr>\r\n<td class=\"summary-form-number\" valign=\"top\"><span class=\"summary-question-number\">";
						if (! $print_doc->summarySeqNumbering)
							$ret .= $iiNumber;
						$ret .= "&nbsp;</span></td>\r\n";
						$ret .= "<td width=\"100%\" class=\"summary-form-question\"><span class=\"summary-question\">";
						$ret .= $ii->text;
						$ret .= "</span></td>\r\n</tr>\r\n";

						//one itemized item needs to be in the summary so lets go thru them all
						$pictureNumber = 1;
						$needCheckedName = !$print_doc->excludeSummaryColumnHeaders;
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

							if ($needCheckedName || $include_report_estimates) {
								//we only really need to add this row for the "inspected"/checked name or the estimate
								$bStartedRow = FALSE;
								if ($needCheckedName) {
									$sNames = getCheckedNames($section_col_headers, $ii_answer->check_array); 
									if (! $print_doc->excludeSummaryColumnHeaders) {
										$ret .= "<tr>\r\n<td></td><td class=\"summary-form-answer\">";
										if (!empty($sNames)) {
											$ret .= "<span class=\"summary-answer\">";
											$ret .= $sNames;
											$ret .= "</span>";
										}
										$bStartedRow = TRUE;
									}
									$needCheckedName = FALSE;
								}

							}

							if ($print_doc->summarySeqNumbering) {
								$ret .= "<tr><td class=\"summary-question-number\">";
								$ret .= $seqNum;
								$ret .= ".";
							} else {
								$ret .= "<tr><td class=\"summary-form-comments\">";
							}
							$ret .= "</td>\r\n";
							$ret .= "<td class=\"summary-form-comments\">\r\n";
							$ret .= "<div class=\"summary-comments\" ";
							$color = ""; $icons = "";
							getSummaryPrintInfo($itemized, $color, $icons, $summary_array);
							$ret .= $color;
							$ret .= " >";
												
							
							if ($print_doc->picsInSummary && $print_options->picturesAboveCommentText) {
								if (! empty($pictmp)) {
									$ret .= $pictmp;
								}
							}
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
								$ret .= $tnum;
							}
							
							$ret .= $itemized->comment;
							
							/*Moved estimates here and changed it to a div*/
							
							if ($include_report_estimates) {
									$plain = $itemized->estimate;
									if ( !empty($plain) && ($print_doc->summaryEstimates)) {
										$ret .= " <div style=\"page-break-before:avoid\" class=\"summary-estimates\">";
										$ret .= $itemized->estimate;
										$ret .= "</div>";
									} 
								}
							
							if ($print_doc->picsInSummary && !$print_options->picturesAboveCommentText) {
								if (! empty($pictmp)) {
									$ret .= "<br />\r\n";
									$ret .= $pictmp;
								}
							}
							$ret .= "</div></td>";
							$ret .= "\r\n</tr>\r\n";
						}
						$ret .= "\r\n</table>\r\n";
					}
				}				
			}
		}
	}
}
echo $ret;
?>
