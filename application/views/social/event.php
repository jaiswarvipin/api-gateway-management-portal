<div class="section"></div>
<main>
	<center>
		<img src="<?php echo SITE_URL.DEFAULT_LOGO?>" class="responsive-img logo-login-container" />
		<div class="container"> 
			<div class="white row fEventCodeContainer">
				<div class='col s12 m12 l12'>
					<h4 class="fEnterEventCodeTitle">Enter your event code to get involved</h4>
					<span class="fEnterEventCodeSubTitle">Your code can normally be found on the public display or provided by the meeting organiser. The code is 6 alphanumeric characters - any issues please contact your event organiser.</span>
					<form method="post" action="<?php echo SITE_URL?>/event/event-code-verify" name="frmEventCodeVerify" id="frmEventCodeVerify" class="fEventFrom">
						<div class='row'>
							<div class='col s12'></div>
						</div>
						<div class='row align-wrapper'>
							<div class="col s12 m12 l12">
								<label for='event-code' class="fEventEnterLabel center-align">Event Code</label>
							</div>
							<div class='input-field col s12 m12 l12'>
								<input class='validate fEventEnterInput center-align' type='text' name='event-code' id='event-code' placeholder="000000" maxlength="6" />
							</div>
						</div>
						<div class='row'>
							<div class='col s12'><span class="fEnterEventCodeSubTitle">By entering this portal you confirm you are permitted to do so, and accept our <a href="javascript:void(0);" class="blue-text text-darken-2 text-bold" onclick="openDialog('divCookieAndPV');">cookie and privacy policy</a>.</span></div>
						</div>
						<div class='row'>
							<div class='col s12 align-wrapper'><button name='cmdEventCodeVerify' id='cmdEventCodeVerify' class='btn btn-large waves-effect blue darken-4 right-align fEventCodeSubmit f25' formName="frmEventCodeVerify">Enter</button></div>
						</div>
					</form>
				</div>
			</div>
			<div class="row powerBy">
				<div class="col l16 m6 s6 right-align">POWERED BY&nbsp;</div>
				<div class='col l6 m6 s6 left-align nml20'><img src="<?php echo SITE_URL?>uploads/company/pa_logo.png" class="responsive-img" width="100px" /></div>
			</div>
		</div>
	</center>
</main>
<!-- Modal Structure -->
<div id="divCookieAndPV" class="modal modal-fixed-footer" style="width: 70% !important;">
	<div class="modal-content">
		<h4>Cookie and Privacy Policy</h4>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	</div>
	<div class="modal-footer">
		<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Disagree </a>
		<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Agree</a>
	</div>
</div>
         