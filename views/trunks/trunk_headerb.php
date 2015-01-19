<?php
$pageheading = sprintf(_("%s %s Trunk"),(empty($extdisplay) ? _('Add'): _('Edit')),$upper_tech);
if(!empty($extdisplay)){
	if ($num_routes > 0){
		$pageinfoinuse .= '<div class="well well-info">';
		$pageinfoinuse .= _("In use by")." ".$num_routes." ".($num_routes == 1 ? _("route") : _("routes"));
		$pageinfoinuse .= '<br/>';
		foreach($routes as $route=>$priority) {
			$pageinfoinuse .= _("Route").'<b>'.$route.'</b>: '._("Sequence").' <b>'.$priority.'</b><br>';
		}
		$pageinfoinuse .= '</div>';
	}else{
		$pageinfoinuse .= '<div class="well well-info">';
		$pageinfoinuse .= _("WARNING:");
		$pageinfoinuse .= _("This trunk is not used by any routes!");
		$pageinfoinuse .= _("This trunk will not be able to be used for outbound calls until a route is setup that uses it. Click on <b>Outbound Routes</b> to setup routing.");
		$pageinfoinuse .= '</div>';
	}
}
if(!empty($helptext)) {
	$pageinfohelp .= '<div class="well well-info">';
	$pageinfohelp .= $helptext;
	$pageinfohelp .= '</div>';
}
$maxchanshelp = _("Controls the maximum number of outbound channels (simultaneous calls) that can be used on this trunk. Inbound calls are not counted against the maximum. Leave blank to specify no maximum.");
switch($tech){
	case "sip":
	case "iax":
	case "iax2":
		$pr_tech = ($tech == "iax") ? "iax2":$tech;
		$maxchanshelp = sprintf(_("Controls the maximum number of outbound channels (simultaneous calls) that can be used on this trunk. To count inbound calls against this maximum, use the auto-generated context: %s as the inbound trunk's context. (see extensions_additional.conf) Leave blank to specify no maximum."),((isset($channelid) && trim($channelid)!="")?"from-trunk-$pr_tech-$channelid":"from-trunk-[trunkname]"));
	break;
	default:
	break;
}
if($failtrunk_enable && $failtrunk || $amp_conf['DISPLAY_MONITOR_TRUNK_FAILURES_FIELD']){
	$mtfhtml = '<!--MONITOR TRUNK FAILURES-->';
	$mtfhtml .= '<div class="element-container">';
	$mtfhtml .= '	<div class="row">';
	$mtfhtml .= '		<div class="col-md-12">';
	$mtfhtml .= '			<div class="row">';
	$mtfhtml .= '				<div class="form-group">';
	$mtfhtml .= '					<div class="col-md-3">';
	$mtfhtml .= '						<label class="control-label" for="failtrunk">'._("Monitor Trunk Failures").'</label>';
	$mtfhtml .= '						<i class="fa fa-question-circle fpbx-help-icon" data-for="failtrunk"></i>';
	$mtfhtml .= '					</div>';
	$mtfhtml .= '					<div class="col-md-9">';
	$mtfhtml .= '						<input'. (!$failtrunk_enable)?"disabled":"".' type="text" class="form-control" name="failtrunk" id="failtrunk" value="'.htmlspecialchars($failtrunk).'"/>';
	$mtfhtml .= '						<span class="radioset">';
	$mtfhtml .= '							<input type="checkbox" tabindex="'. ++$tabindex .'" name="failtrunk_enable" id="failtrunk_enable" value="1" '. ($failtrunk_enable)?'CHECKED':''.' OnClick="disable_field(failtrunk,failtrunk_enable); return true;">';
	$mtfhtml .= '							<label for="failtrunk_enable">'._("Enable").'</label>';
	$mtfhtml .= '						</span>';
	$mtfhtml .= '					</div>';
	$mtfhtml .= '				</div>';
	$mtfhtml .= '			</div>';
	$mtfhtml .= '		</div>';
	$mtfhtml .= '	</div>';
	$mtfhtml .= '	<div class="row">';
	$mtfhtml .= '		<div class="col-md-12">';
	$mtfhtml .= '			<span id="failtrunk-help" class="help-block fpbx-help-block">'._("If checked, supply the name of a custom AGI Script that will be called to report, log, email or otherwise take some action on trunk failures that are not caused by either NOANSWER or CANCEL.").'</span>';
	$mtfhtml .= '		</div>';
	$mtfhtml .= '	</div>';
	$mtfhtml .= '</div>';
	$mtfhtml .= '<!--END MONITOR TRUNK FAILURES-->';
}
$dpmrtop = _("These rules can manipulate the dialed number before sending it out this trunk. If no rule applies, the number is not changed. The original dialed number is passed down from the route where some manipulation may have already occurred. This trunk has the option to further manipulate the number. If the number matches the combined values in the <b>prefix</b> plus the <b>match pattern</b> boxes, the rule will be applied and all subsequent rules ignored.<br/> Upon a match, the <b>prefix</b>, if defined, will be stripped. Next the <b>prepend</b> will be inserted in front of the <b>match pattern</b> and the resulting number will be sent to the trunk. All fields are optional.").'<br /><br />';
$dpmrhtml .= '<b>' . _("Rules:") . '</b><br />';
$dpmrhtml .= '<strong>X</strong>&nbsp;&nbsp;&nbsp;' . _("matches any digit from 0-9") . '<br />';
$dpmrhtml .= '<strong>Z</strong>&nbsp;&nbsp;&nbsp;' . _("matches any digit from 1-9") . '<br />';
$dpmrhtml .= '<strong>N</strong>&nbsp;&nbsp;&nbsp;' . _("matches any digit from 2-9") . '<br />';
$dpmrhtml .= '<strong>[1237-9]</strong>&nbsp;'   . _("matches any digit or letter in the brackets (in this example, 1,2,3,7,8,9)").'<br />';
$dpmrhtml .= '<strong>.</strong>&nbsp;&nbsp;&nbsp;' . _("wildcard, matches one or more characters (not allowed before a | or +)").'<br />';
if(!$amp_conf['ENABLEOLDDIALPATTERNS']) {
	$dpmrtop = _("A Dial Rule controls how calls will be dialed on this trunk. It can be used to add or remove prefixes. Numbers that don't match any patterns defined here will be dialed as-is. Note that a pattern without a + or | (to add or remove a prefix) will not make any changes but will create a match. Only the first matched rule will be executed and the remaining rules will not be acted on.").'<br /><br />';
	$dpmrhtml .= '<strong>|</strong>&nbsp;&nbsp;&nbsp;' . _("removes a dialing prefix from the number (for example, 613|NXXXXXX would match when some dialed \"6135551234\" but would only pass \"5551234\" to the trunk");
	$dpmrhtml .= '<strong>+</strong>&nbsp;&nbsp;&nbsp;' . _("adds a dialing prefix from the number (for example, 1613+NXXXXXX would match when some dialed \"5551234\" and would pass \"16135551234\" to the trunk)").'<br /><br />';
	$dpmrhtml .= _("You can also use both + and |, for example: 01+0|1ZXXXXXXXXX would match \"016065551234\" and dial it as \"0116065551234\" Note that the order does not matter, eg. 0|01+1ZXXXXXXXXX does the same thing.");
}
$pp_tit = _("prepend");
$pf_tit = _("prefix");
$mp_tit = _("match pattern");
$ci_tit = _("CallerID");
//Dialpatterns Form field(s)
$dpinput = array();
if($amp_conf['ENABLEOLDDIALPATTERNS']) {
	foreach ($dialpattern_array as $idx => $pattern) {
		$tabindex++;
		if ($idx == 50) {
			$dpt_title_class = 'dpt-title dpt-nodisplay';
		}
		$dpinput[] = '<tr id = "row'.$idx.'">';
		$dpt_class = $pattern['prepend_digits'] == '' ? $dpt_title_class : 'dpt-value';
		$dpinput[] = '<td>';
		$dpinput[] = '	<div class="input-group">';
		$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'1">(</span>';
		$dpinput[] = '		<input placeholder="' . $pp_tit . '" type="text" id="prepend_digit_'.$idx.'" name="prepend_digit['.$idx.']" class="form-control ' . $dpt_class.'" value="'. $pattern['prepend_digits'].'" tabindex="'.$tabindex++.'">';
		$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'2">)</span>';
		$dpinput[] = '	</div>';
		$dpinput[] = '</td>';
		$dpt_class = $pattern['match_pattern_prefix'] == '' ? $dpt_title_class : 'dpt-value';
		$dpinput[] = '<td>';
		$dpinput[] = '	<div class="input-group">';
		$dpinput[] = '		<input placeholder="'. $pf_tit .'" type="text" id="pattern_prefix_'.$idx.'" name="pattern_prefix['.$idx.']" class="form-control '.$dpt_class.'" value="'.$pattern['match_pattern_prefix'].'" tabindex="'.$tabindex++.'"> ';
		$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'3">|</span>';
		$dpinput[] = '	</div>';
		$dpinput[] = '</td>';
		$dpt_class = $pattern['match_pattern_pass'] == '' ? $dpt_title_class : 'dpt-value';
		$dpinput[] = '<td>';
		$dpinput[] = '	<div class="input-group">';
		$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'4">[</span>';	
		$dpinput[] = '		<input placeholder="'.$mp_tit.'" type="text" id="pattern_pass_'.$idx.'" name="pattern_pass['.$idx.']" class="form-control '.$dpt_class.'" value="'.$pattern['match_pattern_pass'].'" tabindex="'.$tabindex++.'"> ';
		$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'5">/</span>';
		$dpinput[] = '	</div>';
		$dpinput[] = '</td>';
		$dpt_class = $pattern['match_cid'] == '' ? $dpt_title_class : 'dpt-value';
		$dpinput[] = '<td>';
		$dpinput[] = '	<div class="input-group">';
		$dpinput[] = '		<input placeholder="'.$ci_tit.'" type="text" id="match_cid_'.$idx.'" name="match_cid['.$idx.']" class="form-control '.$dpt_class.'" value="'.$pattern['match_cid'].'" tabindex="'.$tabindex++.'">';
		$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'6">]</span>';
		$dpinput[] = '	</div>';
		$dpinput[] = '<td>';
		$dpinput[] = '		<a href="#"  id="rowadd'.$idx.'"><i class="fa fa-plus"></i></a>';
		$dpinput[] = '		<a href="#"  id="rowdel'.$idx.'"><i class="fa fa-trash"></i></a>';
		$dpinput[] = '</td>';
		$dpinput[] = '</tr>';
	}
	//Always an empty row incase there are no patterns.... 
	$next_idx = count($dialpattern_array);
	$idx = !empty($idx) ? $idx : $next_idx;
	$tabindex++;
	if ($idx == 50) {
		$dpt_title_class = 'dpt-title dpt-nodisplay';
	}
	$dpinput[] = '<tr id = "row'.$idx.'">';
	$dpt_class = $pattern['prepend_digits'] == '' ? $dpt_title_class : 'dpt-value';
	$dpinput[] = '<td>';
	$dpinput[] = '	<div class="input-group">';
	$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'1">(</span>';
	$dpinput[] = '		<input placeholder="' . $pp_tit . '" type="text" id="prepend_digit_'.$idx.'" name="prepend_digit['.$idx.']" class="form-control ' . $dpt_class.'" value="'. $pattern['prepend_digits'].'" tabindex="'.$tabindex++.'">';
	$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'2">)</span>';
	$dpinput[] = '	</div>';
	$dpinput[] = '</td>';
	$dpt_class = $pattern['match_pattern_prefix'] == '' ? $dpt_title_class : 'dpt-value';
	$dpinput[] = '<td>';
	$dpinput[] = '	<div class="input-group">';
	$dpinput[] = '		<input placeholder="'. $pf_tit .'" type="text" id="pattern_prefix_'.$idx.'" name="pattern_prefix['.$idx.']" class="form-control '.$dpt_class.'" value="'.$pattern['match_pattern_prefix'].'" tabindex="'.$tabindex++.'"> ';
	$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'3">|</span>';
	$dpinput[] = '	</div>';
	$dpinput[] = '</td>';
	$dpt_class = $pattern['match_pattern_pass'] == '' ? $dpt_title_class : 'dpt-value';
	$dpinput[] = '<td>';
	$dpinput[] = '	<div class="input-group">';
	$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'4">[</span>';	
	$dpinput[] = '		<input placeholder="'.$mp_tit.'" type="text" id="pattern_pass_'.$idx.'" name="pattern_pass['.$idx.']" class="form-control '.$dpt_class.'" value="'.$pattern['match_pattern_pass'].'" tabindex="'.$tabindex++.'"> ';
	$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'5">/</span>';
	$dpinput[] = '	</div>';
	$dpinput[] = '</td>';
	$dpt_class = $pattern['match_cid'] == '' ? $dpt_title_class : 'dpt-value';
	$dpinput[] = '<td>';
	$dpinput[] = '	<div class="input-group">';
	$dpinput[] = '		<input placeholder="'.$ci_tit.'" type="text" id="match_cid_'.$idx.'" name="match_cid['.$idx.']" class="form-control '.$dpt_class.'" value="'.$pattern['match_cid'].'" tabindex="'.$tabindex++.'">';
	$dpinput[] = '		<span class="input-group-addon" id="basic-addon'.$idx.'6">]</span>';
	$dpinput[] = '	</div>';
	$dpinput[] = '<td>';
	$dpinput[] = '		<a href="#"  id="rowadd'.$idx.'"><i class="fa fa-plus"></i></a>';
	$dpinput[] = '		<a href="#"  id="rowdel'.$idx.'"><i class="fa fa-trash"></i></a>';
	$dpinput[] = '</td>';
	$dpinput[] = '</tr>';
	$dprows = implode(PHP_EOL, $dpinput);
}else{
	$dpinput = array();
	$dpinput[] = '<textarea textarea name="bulk_patterns" class="form-control" id="bulk_patterns" rows="10" cols="70">';
	foreach ($dialpattern_array as $pattern){
		$prepend = ($pattern['prepend_digits'] != '') ? $pattern['prepend_digits'].'+' : '';
		$match_pattern_prefix = ($pattern['match_pattern_prefix'] != '') ? $pattern['match_pattern_prefix'].'|' : '';
		$match_cid = ($pattern['match_cid'] != '') ? '/'.$pattern['match_cid'] : '';
		$dpinput[] = $prepend . $match_pattern_prefix . $pattern['match_pattern_pass'] . $match_cid . PHP_EOL;	
	}
	$dpinput[] = '</textarea>';
	$dprows = implode(PHP_EOL, $dpinput);

}

?>
<div class="container-fluid">
	<h1><?php echo $pageheading ?></h1>
	<?php echo $pageinfoinuse ?>
	<?php echo $pageinfohelp ?>
	<div class = "display full-border">
		<div class="row">
			<div class="col-sm-9">
				<div class="fpbx-container">
					<div class="display full-border">
						<form enctype="multipart/form-data" class="fpbx-submit" name="trunkEdit" id="trunkEdit" action="config.php" method="post"  data-fpbx-delete="config.php?display=trunks&amp;extdisplay=<?php echo urlencode($extdisplay) ?>&amp;action=deltrunk">
							<input type="hidden" name="display" value="<?php echo $display?>"/>
							<input type="hidden" name="extdisplay" value="<?php echo $extdisplay ?>"/>
							<input type="hidden" name="action" value="<?php echo ($extdisplay ? "edittrunk" : "addtrunk") ?>"/>
							<input type="hidden" name="tech" value="<?php echo $tech?>"/>
							<input type="hidden" name="provider" value="<?php echo $provider?>"/>
							<input type="hidden" name="sv_trunk_name" value="<?php echo $trunk_name?>"/>
							<input type="hidden" name="sv_usercontext" value="<?php echo $usercontext?>"/>
							<input type="hidden" name="sv_channelid" value="<?php echo $channelid?>"/>
							<input id="npanxx" name="npanxx" type="hidden" />
							<div class="section-title" data-for="genset">
								<h3><i class="fa fa-minus"></i><?php echo _("General Settings") ?></h3>
							</div>
							<div class="section" data-id="genset">
								<!--TRUNK NAME-->
								<div class="element-container">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="form-group">
													<div class="col-md-3">
														<label class="control-label" for="trunk_name"><?php echo _("Trunk Name") ?></label>
														<i class="fa fa-question-circle fpbx-help-icon" data-for="trunk_name"></i>
													</div>
													<div class="col-md-9">
														<input type="text" class="form-control" name="trunk_name" id="trunk_name" value="<?php echo $trunk_name;?>" tabindex="<?php echo ++$tabindex;?>"/>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<span id="trunk_name-help" class="help-block fpbx-help-block"><?php echo _("Descriptive Name for this Trunk")?></span>
										</div>
									</div>
								</div>
								<!--END TRUNK NAME-->
								<!--OUTBOUND CID-->
								<div class="element-container">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="form-group">
													<div class="col-md-3">
														<label class="control-label" for="outcid"><?php echo _("Outbound CallerID") ?></label>
														<i class="fa fa-question-circle fpbx-help-icon" data-for="outcid"></i>
													</div>
													<div class="col-md-9">
														<input type="text" class="form-control" name="outcid" id="outcid" value="<?php echo $outcid;?>" tabindex="<?php echo ++$tabindex;?>"/>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<span id="outcid-help" class="help-block fpbx-help-block"><?php echo _("CallerID for calls placed out on this trunk<br><br>Format: <b>&lt;#######&gt;</b>. You can also use the format: \"hidden\" <b>&lt;#######&gt;</b> to hide the CallerID sent out over Digital lines if supported (E1/T1/J1/BRI/SIP/IAX).")?></span>
										</div>
									</div>
								</div>
								<!--END OUTBOUNDCID-->
								<!--KEEPCID-->
								<div class="element-container">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="form-group">
													<div class="col-md-3">
														<label class="control-label" for="keepcid"><?php echo _("CID Options") ?></label>
														<i class="fa fa-question-circle fpbx-help-icon" data-for="keepcid"></i>
													</div>
													<div class="col-md-9">
														<select name="keepcid" id="keepcid" class="form-control" tabindex="<?php echo ++$tabindex;?>">
													    <?php
														    $default = (isset($keepcid) ? $keepcid : 'off');
														    echo '<option value="off"' . ($default == 'off'  ? ' SELECTED' : '').'>'._("Allow Any CID")."\n";
														    echo '<option value="on"'  . ($default == 'on'   ? ' SELECTED' : '').'>'._("Block Foreign CIDs")."\n";
														    echo '<option value="cnum"'. ($default == 'cnum' ? ' SELECTED' : '').'>'._("Remove CNAM")."\n";
														    echo '<option value="all"' . ($default == 'all'  ? ' SELECTED' : '').'>'._("Force Trunk CID")."\n";
													    ?>
													    </select>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<span id="keepcid-help" class="help-block fpbx-help-block"><?php echo _("Determines what CIDs will be allowed out this trunk. IMPORTANT: EMERGENCY CIDs defined on an extension/device will ALWAYS be used if this trunk is part of an EMERGENCY Route regardless of these settings.<br />Allow Any CID: all CIDs including foreign CIDS from forwarded external calls will be transmitted.<br />Block Foreign CIDs: blocks any CID that is the result of a forwarded call from off the system. CIDs defined for extensions/users are transmitted.<br />Remove CNAM: this will remove CNAM from any CID sent out this trunk<br />Force Trunk CID: Always use the CID defined for this trunk except if part of any EMERGENCY Route with an EMERGENCY CID defined for the extension/device.") . _("Intra-Company Routes will always trasmit an extension's internal number and name.")?></span>
										</div>
									</div>
								</div>
								<!--END KEEPCID-->
								<!--MAXIMUM CHANNELS-->
								<div class="element-container">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="form-group">
													<div class="col-md-3">
														<label class="control-label" for="maxchans"><?php echo _("Maximum Channels") ?></label>
														<i class="fa fa-question-circle fpbx-help-icon" data-for="maxchans"></i>
													</div>
													<div class="col-md-9">
														<input type="number" class="form-control" name="maxchans" id="maxchans" value="<?php echo htmlspecialchars($maxchans); ?>" tabindex="<?php echo ++$tabindex;?>"/>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<span id="maxchans-help" class="help-block fpbx-help-block"><?php echo $maxchanshelp ?></span>
										</div>
									</div>
								</div>
								<!--END MAXIMUM CHANNELS-->
								<!--DIAL OPTS-->
								<div class="element-container">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="form-group">
													<div class="col-md-3">
														<label class="control-label" for="dialopts"><?php echo _('Asterisk Trunk Dial Options') ?></label>
														<i class="fa fa-question-circle fpbx-help-icon" data-for="dialopts"></i>
													</div>
													<div class="col-md-9">
														<input type="text" class="form-control" id="dialopts" name="dialopts" value="<?php echo $dialopts !== false?$dialopts:''?>" <?php echo $dialopts === false?'disabled':''?>>
														<span class="radioset">
															<input type="checkbox" name="dialoutopts_cb" id="dialoutopts_cb" data-disabled="<?php echo $amp_conf['TRUNK_OPTIONS']?>"  >
															<label for="dialoutopts_cb"><?php echo _('Override')?></label>
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<span id="dialopts-help" class="help-block fpbx-help-block"><?php echo _('Asterisk Dial command options to be used when calling out this trunk. To override the Advanced Settings default, check the box and then provide the required options for this trunk')?></span>
										</div>
									</div>
								</div>
								<!--END DIAL OPTS-->
								<!--CONTINUE IF BUSY-->
								<div class="element-container">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="form-group">
													<div class="col-md-3">
														<label class="control-label" for="continuew"><?php echo _("Continue if Busy") ?></label>
														<i class="fa fa-question-circle fpbx-help-icon" data-for="continuew"></i>
													</div>
													<div class="col-md-9">
														<input type='checkbox'  tabindex="<?php echo ++$tabindex;?>" name='continue' id="continue" <?php if ($continue=="on") { echo 'CHECKED'; }?> >
														<label for='continue'><?php echo _("Always try next trunk")?></label>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<span id="continuew-help" class="help-block fpbx-help-block"><?php echo _("Normally the next trunk is only tried upon a trunk being 'Congested' in some form, or unavailable. Checking this box will force a failed call to always continue to the next configured trunk or destination even when the channel reports BUSY or INVALID NUMBER.")?></span>
										</div>
									</div>
								</div>
								<!--END CONTINUE IF BUSY-->
								<!--DISABLE TRUNK-->
								<div class="element-container">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="form-group">
													<div class="col-md-3">
														<label class="control-label" for="disabletrunkw"><?php echo _("Disable Trunk")?></label>
														<i class="fa fa-question-circle fpbx-help-icon" data-for="disabletrunkw"></i>
													</div>
													<div class="col-md-9">
														<span class="radioset">
															<input type='checkbox'  tabindex="<?php echo ++$tabindex;?>"name='disabletrunk' id="disabletrunk" <?php if ($disabletrunk=="on") { echo 'CHECKED'; }?> OnClick='disable_verify(disabletrunk); return true;'>
															<label for='disabletrunk'><?php echo _("Disable")?></label>
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<span id="disabletrunkw-help" class="help-block fpbx-help-block"><?php echo _("Check this to disable this trunk in all routes where it is used.")?></span>
										</div>
									</div>
								</div>
								<!--END DISABLE TRUNK-->
								<?php echo $mtfhtml ?>

							</div>
							<div class="section-title" data-for="dnmr">
								<h3><i class="fa fa-minus"></i><?php echo _("Dialed Number Manipulation Rules") ?></h3>
							</div>
							<div class="section" data-id="dnmr">
								<br/>
								<br/>
								<h3><?php echo _("Dial Number Manipulation Rules")?></h3>
								<?php echo $dpmrtop?>
								<?php echo $dpmrhtml?>
								<table class="table table-striped" id="dptable">
								<?php echo $dprows ?>
								</table>
							</div>
<!--End of trunk_header-->
