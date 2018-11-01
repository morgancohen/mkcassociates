<?php include ('hgfunctions.php') ?>

<?php
$subnum = 0;
$printedKey = 0;
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
            $ch .= " ";
        $omg = 1;
        $ch .= "<span class=\"form-key\">";
        $ch .= $section_col_headers[$i]->shortName;
        $ch .= "&nbsp;=&nbsp;";
        $ch .= $section_col_headers[$i]->longName;
        $ch .= "</span>&nbsp;&nbsp;&nbsp;&nbsp;\r";
		$iih .= "<td class=\"form-header\" style=\"width: 35px\"><span class=\"abbrev\">";
        $iih .= $section_col_headers[$i]->shortName;
        $iih .= "</span></td>\r";
    }
}

	if ($print_options->pageBreaksBetweenSections) { ?>
		<div class="screen-page" style="page-break-before: always;">
<?php } else { ?>
		<div class="screen-page">
<?php } ?>
	
<table class="form" cellpadding="2" cellspacing="0">
<!--Form key-->
<thead class="form">
<tr>
<td colspan="<?php echo sizeof($section_col_headers)+2;?>">
<?php if ($printedKey == 0) {
	//$printedKey = 1; ?>
	<?php echo $ch; ?>
<?php } ?>
</td>
</tr>
<tr>
<?php echo $iih; ?>
<td class="form-header">&nbsp;</td>
<td class="form-header-title"><span class="abbrev">&nbsp;</span></td>
</tr>
</thead>

<tbody class="form">
<tr class="section-header-tx-bg">
<td colspan="<?php echo sizeof($section_col_headers);?>">&nbsp;</td>
<td colspan="2">
<!--section header-->
<div class="section-header-tx">
	<a name="<?php echo getAnchorName($section_answer_repeat)?>" id="<?php echo getAnchorName($section_answer_repeat)?>"></a>
	<span class="header-number"><?php echo getSectionDisplayLabel($subnum, $section_answer_repeat->repeat_number, sizeof($section_answer->answer_repeat_array)); ?></span>

	<?php echo getSectionName($section_answer_repeat); ?>
</div>

<!--intro text and pics-->
<?php if (! empty($section_answer_repeat->intro)) { ?>
	<div class="introtext">
<?php	echo $section_answer_repeat->intro; ?>
	</div>
<?php }

	if ($print_doc->picsInReport && (sizeof($section_answer_repeat->picture_array) > 0)) {
		$na = 0;
		$forceCenter = 1;
		echo buildHorzPictureTable($section_answer_repeat->picture_array, "", $na, $forceCenter);
	}
?>

</tr>



			<!--This is the Inspection Items-->
<?php
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

                $iir = "<tr>\r";
				//build check cells
                for ($i = 0; $i < sizeof($section_col_headers); $i++) {
                    if (!$section_col_headers[$i]->excludeColumn) { 
                        $iir .= "<td class=\"form-answer\">";
                        if ($ii_answer->check_array[$i])
                            $iir .= "<img src=\"images/txcheck.gif\" /></td>\r";
                        else
                            $iir .= "<img src=\"images/txuncheck.gif\" /></td>\r";
                    }
                }
                $ret .= $iir;

				$pictureIndex = 1;
                $pictureLabel = $s;
                $pictureLabel .= " ";
                $pictureLabel .= $print_options->mediaName;

                $ret .= "<td class=\"form-question-number\"><span class=\"question-number\">";
                $ret .= $s;
                $ret .= "</span></td>\r\n";
                $ret .= "<td class=\"form-question\"><span class=\"question\">";
                $ret .= $ii->text;
                $ret .= "</span><br />";

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

	                $insertedSM++;
	                $oneSM .= "<span class=\"sm-header\" >";
	                $oneSM .= $sm->name;
	                $oneSM .= ":&nbsp;&nbsp;</span>\r";
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
	                        $oneSM .= ": ";
	                        $oneSM .= $sm_answer->item;
	                        $oneSM .= "</span><br />\r";
	                    }
	                }
	            	$ret .= $oneSM;
	            }
        $ret .= "<span class=\"comments-header\">";
        $ret .= htmlspecialchars($print_options->commentName);
        $ret .= "</span><br />\r";
        $commentsNameInserted = 1;
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
                    if (! empty($commentText) || ! empty($picText) || ! empty($icons)) {
                        //we need to put something in

                        if ($print_options->picturesAboveCommentText && ! empty($picText)) {
                            $ret .= $picText;
                        }
						$ret .= $icons;
						$ret .= "\r\n<span class=\"comments\"";
                        $ret .= $color;
                        $ret .= ">";

                        if (sizeof($ii_answer->item_array) > 1 && $print_options->addItemizeNumbers) {
                            //if there is more than one itemized item then we
                            //prefix the item with the item number
                            $ret .= '(';
                            $ret .= $xx + 1;
                            $ret .= ') ';
                        }

                        $ret .= $commentText;
                        $ret .= "\r</span>";

                        if (! $print_options->picturesAboveCommentText && ! empty($picText)) {
                            $ret .= $picText;
                        }
                        $ret .= "<br />\r\n";
                    }
                }
                $ret .= "\r</td>\r</tr>\r\n";
            }

            echo $ret;
?>
</table>

<?php 	if (!empty($section_answer_repeat->footer)) {
			?><div class="footertext">
			<?php echo $section_answer_repeat->footer; ?>
			</div>
<?php 	} ?>
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
