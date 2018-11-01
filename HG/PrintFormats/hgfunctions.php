<?php

$isPDF = $print_doc->outputPDF;

function getSectionName($section_answer_repeat) {
	if (empty($section_answer_repeat->name)) { //use repeat header instead
		$name = $section_answer_repeat->section->name;
	} else {
		$name = $section_answer_repeat->name;
	}
	return $name;
}

function getAnchorName($section_answer_repeat) {
	$anchorname = getSectionName($section_answer_repeat);
	//remove all things not alphanum
	$anchorname = html_entity_decode($anchorname, ENT_QUOTES);
	$anchorname = strip_tags($anchorname);
	$anchorname = preg_replace("/[^a-zA-Z0-9s]/", "", $anchorname);
	return $anchorname;
}

function getAlphaIndex($i) {
	$ret = "";
	do {
		$aa = $i % 26;
		$ret .= chr(65 + $aa);
		$i -= 26;
	} while ($i >= 0);
	
	return $ret;
}
// A function to return the Roman Numeral, given an integer
function numberToRoman($num)  {
     // Make sure that we only use the integer portion of the value
     $n = intval($num);
     $result = '';
 
     // Declare a lookup array that we will use to traverse the number:
     $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
     'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
     'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
 
     foreach ($lookup as $roman => $value)   {
         // Determine the number of matches
         $matches = intval($n / $value);
 
         // Store that many characters
         $result .= str_repeat($roman, $matches);
 
         // Substract that from the number
         $n = $n % $value;
     }
 
     // The Roman numeral should be built, return it
     return $result;
}
 
//
// give a list of col headers and answers for those - create a displayable string
// of the ones that are selected
// 
function getCheckedNames($col_header_array, $check_array) {
	$names = '';
	for ($i = 0; $i < sizeof($check_array); $i++) {
		if ($col_header_array[$i]->excludeColumn && $check_array[$i]) {; //excludes to its answered
			return '';
		}
		if ($col_header_array[$i]->printColumn && $check_array[$i]) { //it is checked and a printed col so add it
			if (! empty($names)) $names .= ", ";
			$names .= $col_header_array[$i]->longName;
		}
	}
	return $names;
}
 
 
//
//  see if the section has its own column headers or
//  do we use the default template wide ones
//
function getSectionsColHeaders($section) {
	global $col_header_array;
	if ($section == NULL) return $col_header_array;
	if (sizeof($section->col_header_array) == 0) {
		//no headers defined here so use default
		return $col_header_array;
	} else {
		//section has headers so use them
		return $section->col_header_array;
	}
} 

//
// given the list of colheaders and the answers for those
// determine if that II has been excluded.
//
// 
function isIIExcluded($col_header_array, $checks_array) {
	for ($i = 0; $i < sizeof($checks_array); $i++) {
		if ($col_header_array[$i]->excludeColumn && $checks_array[$i]) return TRUE; //excluded so do not print
	}
	return FALSE;
}

function getSummaryPrintInfo($item, &$color, &$icons, $summary_array) {
	$color = '';
	$icons = '';
	for ($sumCnt = 0; $sumCnt < sizeof($item->summary_array); $sumCnt++) {
		for ($index = 0; $index < sizeof($summary_array); $index++) {
			$summary = $summary_array[$index];
			if ($summary->id == $item->summary_array[$sumCnt]) {
				if ($summary != NULL) {
					if ($summary->useColor && empty($color) ) {
						//we only use the first color we find
						$color = "style=\"color:";
						$color .= $summary->color;
						$color .= " !important; \"";
					}
					if ($summary->useIcon) {
						$icons .= "<img class=\"summaryIcon\" src=\"";
						$icons .= $summary->iconFilename;
						$icons .= "\" />";
					}
				}
			}
		}
	}				
}

function getSectionDisplayLabel($sectionIndex, $currepeat, $maxrepeat) {
	global $template;
	if ($template->useRomanSections) {
		if ($sectionIndex < 0) {
			$ret = "x";
		} else {
			$ret = numberToRoman($sectionIndex);
		}
		//put the repeat count on the end
		if ($maxrepeat > 1) {
			$ret .= "(";
			$ret .= (string)($currepeat+1);
			$ret .= ") ";
		} 
	} else {
		if ($sectionIndex < 0) {
			$ret = "X";
		} else {
			$ret = (string)$sectionIndex;
		}
		//put the repeat count on the end
		if ($maxrepeat > 1) {
			$ret .= "(";
			$ret .= getAlphaIndex($currepeat);
			$ret .= ") ";
		} 
	}
	$ret .= ". &nbsp;";	
	return $ret;
}


function getInspectionItemDisplayNumber($sectionIndex, $itemIndex, $currepeat, $maxrepeat) {
	global $template;
	$ret = '';
	if ($template->useLettersForII) {
		//add the question number
		if ($itemIndex < 0) {
			$ret = "x";
		} else {
			$ret = getAlphaIndex($itemIndex); 
		}

		//put the repeat count on the end
		if ($maxrepeat > 1) {
			$ret .= '.';
			$ret .= ($currepeat+1);
			$ret .= '. ';
		} else {
			$ret .= '. ';
		}
	} else {
		//get the subcategory this is
		if ($sectionIndex < 0) {
			$ret = "X";
		} else {
			$ret .= $sectionIndex;
		}

		$ret .= ".";

		//add the question number
		if ($itemIndex < 0) {
			$ret .= "x";
		} else {
			$ret .= $itemIndex;
		}

		//put the repeat count on the end
		if ($maxrepeat > 1) {
			$ret .= ".";
			$ret .= getAlphaIndex($currepeat);
			$ret .= " ";
		} else {
			$ret .= " ";
		}
	}
	return $ret;
}

function getVideoTag($posterArg, $videoArg, $idArg) {
      // the img and video tags also come out of the software for *replacements* for cover page images
      $ret = "<div width=\"320\" height=\"240\" class=\"novideoimage\">";
      $ret .= "<img width=\"320\" height=\"240\" class=\"poster\" src=\"";
      $ret .= $posterArg;
      $ret .= "\">";
      $ret .= "<img width=\"320\" height=\"240\" class=\"teaser\" src=\"";
      $ret .= "video-print.png";
      $ret .= "\">";      
      $ret .= "</div>";
      $ret .= "<video  id=\"player";
	  $ret .= $idArg;
	  $ret .= "\" class=\"video-js vjs-default-skin vjs-big-play-centered\" width=\"320\" height=\"240\" preload=\"none\" poster=\"";
      $ret .= $posterArg;
      $ret .= "\" controls data-setup=''><source src=\"";
      $ret .= $videoArg;
      $ret .= "\"  type=\"video/mp4\"></video>";
      return $ret;
}


function getPicData($pic, $label, &$indexStart, $centered) {
	global $print_options;
	$pichtml = "";
	if ($pic->video > 0) {
		if ($centered) {
			$pichtml .= "<div class=\"pic-box\"";
			$pichtml .= " style=\"text-align: center; width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
		} else {
			$pichtml .= "<div class=\"pic-wrap\" style=\"width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
		}

		if ($centered) {
			$pichtml .= "<div class=\"vid-box-centered\" style=\"width: ";
			$pichtml .= $pic->width;
			$pichtml .= "px; \">";
		} else {
			$pichtml .= "<div class=\"vid-box\">";
		}

    $pichtml .= getVideoTag($pic->videoThumbnail, $pic->filename, $pic->video);
    $pichtml .= "</div>\r\n";
	} else {
		if ($centered) {
			$pichtml .= "\r<div class=\"pic-box\" ";
			$pichtml .= "style=\"text-align:center; page-break-inside:avoid; width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
//				$pichtml .= "<div class=\"pic-center\">";
		} else {
			$pichtml .= "\r<div class=\"pic-wrap\" ";
			$pichtml .= "style=\"page-break-inside:avoid; width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
//				$pichtml .= "<div class=\"pic-wrap\">";
		}
		$pichtml .="<img class=\"comments-picture\" src=\"";
		$pichtml .= $pic->filename;
		$pichtml .= "\" width=\"";
		$pichtml .= $pic->width;
		$pichtml .= "\" height=\"";
		$pichtml .= $pic->height;
		$pichtml .= "\" />\r";
	}

	if ($print_options->pictureLabels && ! empty($label) ) {
		if ($centered)
			$pichtml .= "<div style=\"text-align: center;\" class=\"comments\">";
		else
			$pichtml .= "<div class=\"comments\">";
      
    $replaced = $label;
    $replaced = str_replace("*num*", $indexStart, $replaced);
    $replaced = str_replace("*type*", $pic->video ? "Video" : "Picture", $replaced);
      
		//$pichtml .= $label;
		//$pichtml .= (string)$indexStart;
    $pichtml .= $replaced;

		$indexStart++;
		if (! empty($pic->label)) {
			$pichtml .= " ";
			$pichtml .= $pic->label;
		}
		$pichtml .= "</div>\r";

	} else {
		if (! empty($pic->label)) {
			$pichtml .= "<div class=\"comments\">";
			$pichtml .= $pic->label;
			$pichtml .= "</div>\r";
		}
	}
	$pichtml .= "</div>\r";
	return $pichtml;
}


function getPicDataHMA($pic, $label, &$indexStart, $centered) {
	global $print_options;
	$pichtml = "";
	if ($pic->video > 0) {
		if ($centered) {
			$pichtml .= "<div class=\"pic-box-hma\"";
			$pichtml .= " style=\"text-align: center; width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
		} else {
			$pichtml .= "<div class=\"pic-wrap-hma\" style=\"width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
		}

		if ($centered) {
			$pichtml .= "<div class=\"vid-box-centered\" style=\"width: ";
			$pichtml .= $pic->width;
			$pichtml .= "px; \">";
		} else {
			$pichtml .= "<div class=\"vid-box\">";
		}

    $pichtml .= getVideoTag($pic->videoThumbnail, $pic->filename, $pic->video);
    $pichtml .= "</div>\r\n";
} else {
		if ($centered) {
			$pichtml .= "\r<div class=\"pic-box-hma\" ";
			$pichtml .= "style=\"text-align:center; page-break-inside:avoid; width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
//				$pichtml .= "<div class=\"pic-center\">";
		} else {
			$pichtml .= "\r<div class=\"pic-wrap-hma\" ";
			$pichtml .= "style=\"page-break-inside:avoid; width: ";
			$pichtml .= $pic->width + 2;
			$pichtml .= "px\">";
//				$pichtml .= "<div class=\"pic-wrap\">";
		}
		$pichtml .="<img class=\"comments-picture\" src=\"";
		$pichtml .= $pic->filename;
		$pichtml .= "\" width=\"";
		$pichtml .= $pic->width;
		$pichtml .= "\" height=\"";
		$pichtml .= $pic->height;
		$pichtml .= "\" />\r";
	}

	if ($print_options->pictureLabels && ! empty($label) ) {
		if ($centered)
			$pichtml .= "<div style=\"text-align: center;\" class=\"comments\">";
		else
			$pichtml .= "<div class=\"comments\">";
      
		//$pichtml .= $label;
		//$pichtml .= (string)$indexStart;

    $replaced = $label;
    $replaced = str_replace("*num*", $indexStart, $replaced);
    $replaced = str_replace("*type*", $pic->video ? "Video" : "Picture", $replaced);
    $pichtml .= $replaced;


		$indexStart++;
		if (! empty($pic->label)) {
			$pichtml .= " ";
			$pichtml .= $pic->label;
		}
		$pichtml .= "</div>\r";

	} else {
		if (! empty($pic->label)) {
			$pichtml .= "<div class=\"comments\">";
			$pichtml .= $pic->label;
			$pichtml .= "</div>\r";
		}
	}
	$pichtml .= "</div>\r";
	return $pichtml;
}


function buildHorzPictureTable($picture_array, $label, &$indexStart, $forceCenter) {
	global $print_options;
//	$HTML5 = ($print_options->IEVersion >= 8);
//	if (! $HTML5) {
//		return buildOldHorzPicTable($picture_array, $label, $indexStart, $forceCenter);
//	}

	$pichtml = '';
		
	//make sure there is at least something
	if (sizeof($picture_array) == 0) return "";

	//make sure they are not all empty placeholders
	$cnt = 0;
	foreach ($picture_array as $pic) {
		if ($pic->filename == 'placeholder.jpg') continue;
		$cnt++;
	}
	if ($cnt == 0) return "no valid pics";

	$centered = (($print_options->centerPics == 1) || $forceCenter);

	if ($centered) {
		$pichtml = "\r\n<div style=\"text-align: center; width: 100%; margin-top:5px; \" class=\"pic-center-aligned\">";
	} else {
		$pichtml = "\r\n<div style=\"text-align: left; margin-top:5px; \" class=\"pic-left-aligned\">";
	}
	
	$maxPicWidth = 0;
	
	foreach ($picture_array as $pic) {
		//add in div separators when printing to pdf
		$maxPicWidth = $maxPicWidth + $pic->width;
		
		if ($maxPicWidth > 700)   {
			$pichtml .= "<div style=\"display:none;\" class=\"pdf-picture-break\"></div>\r\n";
			$maxPicWidth = 0;
		} 
		
		if ($pic->filename == 'placeholder.jpg') continue;
		$pichtml .= getPicData($pic, $label, $indexStart, $centered);

		if ($maxPicWidth > 550)   {
			$pichtml .= "<div style=\"display:none;\" class=\"pdf-picture-break\"></div>\r\n";
			/*  included the inline style so it doesn't affect any custom stylesheets
				need to add this to all styles for @media print
				.pdf-picture-break {
					  display:block !important; clear:both; 
				}
			*/
			$maxPicWidth = 0;
		} 

		
	}
	if (!empty($pichtml)) {
		$pichtml .= "<div style=\"clear: both;\"></div>\r\n";
	}
	
	$pichtml .= "</div>\r\n";

	return $pichtml;
}

function buildOldHorzPicTable($picture_array, $label, $indexStart, $forceCenter)
{
	global $print_options;

	$pichtml = '';
		
	//make sure there is at least something
	if (sizeof($picture_array) == 0) return "";

	//make sure they are not all empty placeholders
	$cnt = 0;
	foreach ($picture_array as $pic) {
		if ($pic->filename == 'placeholder.jpg') continue;
		$cnt++;
	}
	if ($cnt == 0) return "no valid pics";

	$rowsize = 0;
	$centered = (($print_options->centerPics == 1) || $forceCenter);

	$pichtml .= "<table class=\"";
	if ($centered)
		$pichtml .= "pic-table-centered";
	else
		$pichtml .= "pic-table";
	$pichtml .= "\">\r\n<tr valign=\"top\">";

	foreach ($picture_array as $pic) {
		if ($pic->filename == 'placeholder.jpg') continue;

		if ($pic->video > 0)
			$rowsize += 320;
		else
			$rowsize += $pic->width;
		if ($rowsize > $print_options->maxPicWidth) {
			$pichtml .= "\r\n</tr>\r\n</table>";
			$pichtml .= "<table class=\"";
			if ($centered)
				$pichtml .= "pic-table-centered";
			else
				$pichtml .= "pic-table";
			$pichtml .= "\">\r\n<tr valign=\"top\">";
			$rowsize = $pic->width;
		}

		if ($centered) {
			$pichtml .= "<td style=\"text-align: center;\">\r\n";
		} else {
			$pichtml .= "<td width=\"" . $pic->width . "\" height=\"" . $pic->height . "\">\r\n";
		}
			
		if ($pic->video > 0) {
			if ($centered) {
				$pichtml .= "<div style=\"text-align: center; width: 100%;\"><div style=\"margin-left: auto; margin-right: auto; width: ";
				$pichtml .= $pic->width;
				$pichtml .= "px; \">";
			} else {
				$pichtml .= "<div><div class=\"vid-box\">";
			}

      $pichtml .= getVideoTag($pic->videoThumbnail, $pic->filename, $pic->video);

			$pichtml .= "</div>\r\n";
		} else {
			if ($centered) {
				$pichtml .= "<div style=\"text-align: center; margin-left: auto; margin-right: auto; width: ";
				$pichtml .= $pic->width;
				$pichtml .= "px\">\r\n";
			} else {
				$pichtml .= "<div>\r\n";
			}

			$pichtml .= "<img class=\"comments-picture\" src=\"" . $pic->filename . "\"";
			$pichtml .= "width=\"" . $pic->width . "\" height=\"" . $pic->height . "\"/>";
		}

		if ($print_options->pictureLabels && ! empty($label) ) {
			if ($pic->video == 0)
				$pichtml .= "<br/>\r";
			$pichtml .= "<span style=\"text-align: center;\" class=\"comments\">";
			$pichtml .=  $label;
			$pichtml .=  (string)$indexStart;

			$indexStart++;
			if (! empty($pic->label)) {
				$pichtml .=  " ";
				$pichtml .= $pic->label;
			}
			$pichtml .= "</span>\r";

		} else {
			if (! empty($pic->label)) {
				if ($pic->video == 0)
					$pichtml .= "<br/>\r";
				$pichtml .= "<span class=\"comments\">";
				$pichtml .= $pic->label;
				$pichtml .= "</span>\r";
			}
		}

		$pichtml .= "</div>\r\n</td>\r\n";
	}
	$pichtml .= "</tr>\r\n</table>\r\n";

	return $pichtml;
}

function getSinglePic($picture_array, $label, &$indexStart, $forceCenter) {
	global $print_options;

	//make sure they are not all empty placeholders
	$cnt = 0;
	foreach ($picture_array as $pic2) {
		if ($pic2->filename == 'placeholder.jpg') continue;
		$cnt++;
	}
	if ($cnt == 0) return "no valid pics";

	$pic = $picture_array[0];

	$pichtml = "<div class=\"textim2\">";

	if ($pic->video > 0) {
		$pichtml .= "<div class=\"pic-box\"";
		$pichtml .= " style=\"text-align: center; width: ";
		$pichtml .= $pic->width + 2;
		$pichtml .= "px\">\r";

		$pichtml .= "<div class=\"vid-box-centered\" style=\"width: ";
		$pichtml .= $pic->width;
		$pichtml .= "px; \">\r";

        $pichtml .= getVideoTag($pic->videoThumbnail, $pic->filename, $pic->video);
		$pichtml .= "</div>\r\n";
	} else {
		$pichtml .= "<div style=\"text-align: center;\">";
	//	$pichtml .= $pic->width + 2;
		$pichtml .= "<img class=\"comments-picture\" src=\"" . $pic->filename . "\" ";
		$pichtml .= "width=\"" . $pic->width . "\" height=\"" . $pic->height . "\"/>";
	}

	if ($print_options->pictureLabels && ! empty($label) ) {
		$pichtml .= "<div style=\"text-align: center;\" class=\"comments\">";
    $replaced = $label;
    $replaced = str_replace("*num*", $indexStart, $replaced);
    $replaced = str_replace("*type*", $pic->video ? "Video" : "Picture", $replaced);
    $pichtml .= $replaced;
    
		//$pichtml .=  $label;
		//$pichtml .=  (string)$indexStart;

		$indexStart++;
		if (! empty($pic->label)) {
			$pichtml .=  " ";
			$pichtml .= $pic->label;
		}
		$pichtml .= "</div>\r";

	} else {
		if (! empty($pic->label)) {
			$pichtml .= "<div class=\"comments\">";
			$pichtml .= $pic->label;
			$pichtml .= "</div>\r";
		}
	}
	$pichtml .= "</div>\r</div>\r";
	return $pichtml;
}

function getSectionPics($picture_array, $label, &$indexStart, $forceCenter) {
	global $print_options;
	$pichtml = "";
	
	//make sure there is at least something
	if (sizeof($picture_array) == 0) return "";
	//make sure they are not all empty placeholders
	$cnt = 0;
	foreach ($picture_array as $pic) {
		if ($pic->filename == 'placeholder.jpg') continue;
		$cnt++;
	}
	if ($cnt == 0) return "";

	$centered = (($print_options->centerPics == 1) || $forceCenter);
	
	foreach ($picture_array as $pic) {	
		if ($pic->filename == 'placeholder.jpg') continue;
		$pichtml .= getPicData($pic, $label, $indexStart, $centered);
	}

	return $pichtml;
}


function isAnyItemizedIteminSummary($item, $summaryID, $col_header_array) {
	if (isIIExcluded($col_header_array, $item->check_array)) return FALSE;
	foreach ( $item->item_array as $itemized) {
		foreach ($itemized->summary_array as $summary) {
			if ($summary == $summaryID) return TRUE;
		}
	}
	return FALSE; 
}

function isInSummary($itemized, $summaryID) {
	foreach ($itemized->summary_array as $summary) {
		if ($summary == $summaryID) return TRUE;
	}
	return FALSE;	
}

// list of pointers must be cleaned up by calling method
function getListOfPrintedSummaries($summary_array, $pPrintDoc) {
	//find the name of the first printed summary
	if ($pPrintDoc->summaryPrintAll) {
		//print all of the summaries
		return $summary_array;
	} else {
		//print only the list of summary ids that we can find
		$ret_summary_array = array();
		foreach ($pPrintDoc->summaryIDs as $summaryID) {
			foreach ($summary_array as $summary) {
				if ($summary->id == $summaryID) {
					$ret_summary_array[] = $summary;
				}
			}
		}
		return $ret_summary_array;
	}
}


?>
