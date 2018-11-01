<?php include ('hgfunctions.php') ?>


<?php
$subnum = 0;
foreach ($sc_array as $section_container) {
    foreach ($section_container->section_array as $section) {
        $section_answer = $section->section_answer;
        if ($section_answer->excluded) {
            if (!$template->renumberOnExclude)
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
<div class="section-header"><strong>
	<a name="<?php echo getAnchorName($section_answer_repeat) ?>" id="<?php echo
getAnchorName($section_answer_repeat) ?>"></a> 
    
    <?php echo getSectionDisplayLabel($subnum, $section_answer_repeat->
repeat_number, sizeof($section_answer->answer_repeat_array)); ?>
    
    <?php echo getSectionName($section_answer_repeat); ?>
    </strong></div>


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
            $title = "<span class=\"sm-title\" >";
            $title .= htmlspecialchars($print_options->smName);
            $title .= "</span>";
            $tmp = $title;
            for ($y = 0; $y < sizeof($section->sm_array); $y++) {
                $oneSM = '';
                $sm = $sm_array[$section->sm_array[$y]];
                $sm_answer = $section_answer_repeat->sm_answer_array[$y];
                if ($sm_answer->excluded)
                    continue;
                $insertedSM++;

                $oneSM .= "<div class=\"sm-header\" >";
                $oneSM .= $sm->name;
                $oneSM .= ":</div>";
                for ($z = 0; $z < sizeof($sm->option_array); $z++) {
                    $oneSM .= "<span class=\"sm-answer\">( ) ";
                    $oneSM .= $sm->option_array[$z];
                    $oneSM .= ", &nbsp;</span>";
                }
                if (! empty($sm->extraInfoName)) {
                    if (! empty($sm_answer->item)) {
                        $oneSM .= "<span class=\"sm-answer\"><strong>";
                        $oneSM .= $sm->extraInfoName;
                        $oneSM .= " : ";
                        $oneSM .= $sm_answer->item;
                        $oneSM .= "<br /></strong></span>";
                    }
                }
                $tmp .= $oneSM;
            }
            $snm = $tmp;
/*
            if ($insertedSM > 0) {
                //now round out the table with proper amount of cells
                if ($insertedSM % 3 == 0) {
                    //it came out just right so no extra needed
                } else {
                    if ($insertedSM % 3 == 2) {
                        $tmp .= "<span>&nbsp;</span>";
                    } else {
                        if ($insertedSM % 3 == 1) {
                            $tmp .= "<span>&nbsp;</span><span>&nbsp;</span>";
                        }
                    }
                }

                

            }
*/
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
                    $iih .= "<td align=\"center\" width=\"4%\"><span class=\"abbrev\">";
                    $iih .= $section_col_headers[$i]->shortName;
                    $iih .= "</span></td>";
                }
            }
?>
<!--End-->
<!--Inspection Items-->
<table width="100%" cellpadding="5" cellspacing="0">
<tr><td style="vertical-align:top" width="65%">
<table class="form">
<thead class="form">
<tr>
<td colspan="2">&nbsp;</td>
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
                    if (!$template->renumberOnExclude)
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
                $iir .= "<tr><td style=\"text-align:center\" width=\"4%\" class=\"form-question-number\"><span class=\"question-number\"><strong>";
                $iir .= $s;
                $iir .= "</strong></span></td>\r\n";
                $iir .= "<td width=\"50%\" class=\"form-question\" ><span class=\"question\"><strong>";
                $iir .= $ii->text;
                $iir .= "</strong></span></td>";

                for ($i = 0; $i < sizeof($section_col_headers); $i++) {
                    if (!$section_col_headers[$i]->excludeColumn) { //excludes to its answered
                        $iir .= "<td width=\"4%\" class=\"form-answer\">";
                        if ($ii_answer->check_array[$i])
                            $iir .= "<span class=\"answer\">X</span></td>";
                        else
                            $iir .= "<span class=\"answer\">&nbsp;</span></td>";
                    }
                }

                $iir .= "</tr>\r\n";
            }
            echo $iir;
            unset($iir);
?>
</tbody>
</table></td>
</tr>
</table>
<?php echo $snm; ?>
<!--End-->

</div>
<!-- end screen-page -->
<?php
        } //end section repeat answers
    } //end section array
} // end section container array

?>
