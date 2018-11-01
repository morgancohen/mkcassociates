
<div class="screen-page">
<div name="sec-id-workscope">
<a name="workscope" id="workscope">&nbsp;</a>

<h2 style="text-align: center">Work Scope</h2>

<?php 
// header
if (! empty($workscope->intro)) { ?>
<div class="workscope-header">
<?php echo $workscope->intro; ?>
</div>
<div class="workscope-header-spacer">&nbsp</div>
<?php } ?>

<table class="workscope-table">
<!-- jobs table header -->
<thead><tr>
	<th class="workscope-table-header1">Project</th>
	<th class="workscope-table-header2">Sub-Items</th>
<?php
if ($print_options->workscope_estimates) { ?>
	<th class="workscope-table-header3">Estimate</th>
<?php } else { ?>
	<th class="workscope-table-header3">&nbsp;</th>
<?php } ?>
	<th class="workscope-table-header4">&nbsp;</th>
</tr></thead>

<!-- jobs in table -->
<?php foreach ($workscope_job_array as $workscope_job) { ?>
<tr><td colspan="2" class="workscope-cell-jobname">
<?php echo $workscope_job->name ?>
</td>
<td class="workscope-cell-jobest">
<?php if ($print_options->workscope_estimates) {
		echo $workscope_job->estimate;
	 } else {
		echo "&nbsp";
} ?>
</td>
<td class="workscope-cell-jobextra">&nbsp;</td>
</tr>

<!-- items in job -->
<?php
$entryindex = 1;
foreach ($workscope_job->entry_array as $workscope_entry) { ?>
<tr> <td colspan="4" style="width:100%"> 
<!-- item table -->
<table class="workscope-entry-table">
<tr>
<td class="workscope-cell-priority">&nbsp;
<?php
if ($print_options->numberWorkScopeEntries) {
	echo $entryindex;
	$entryindex++;
} ?>
</td>
<td class="workscope-cell-name"> <?php echo $workscope_entry->name ?></td>
<td class="workscope-cell-est">
<?php
	if ($print_options->workscope_estimates) {
		echo $workscope_entry->estimate;
	} else {
		echo "&nbsp;";
	} ?>
</td>
<td>&nbsp;</td>
</tr> 
<tr>
<td colspan="4" class="workscope-cell-desc"><?php echo $workscope_entry->desc ?></td>
</tr>

<?php
if (($print_options->workscope_details) && (! empty($workscope_entry->info))) { ?>
<tr>
<td colspan="4" class="workscope-cell-xinfo"><?php echo $workscope_entry->info ?></td>
</tr>
<?php } ?>

</table>
</td>
</tr>
<?php } // foreach entry
} // foreach job

// grand total
if ($print_options->workscope_estimates) { ?>
<tr>
<td colspan="3" class="workscope-cell-total">Grand Total: $<?php echo $workscope->total ?></td>
<td class="workscope-cell-total">&nbsp;</td>
</tr>
<?php } ?>

</table>

<?php 
// footer
if (! empty($workscope->footer)) { ?>
<div class="workscope-header-spacer">&nbsp</div>
<div class="workscope-footer">
<?php echo $workscope->footer; ?>
</div>
<?php } ?>
</div>

</div>

<?php	if ($print_options->pageBreaksBetweenSections) { ?>
			<div class="page-break" style="page-break-after:always;"></div>
	<!-- end screen-page -->
<?php	}
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
