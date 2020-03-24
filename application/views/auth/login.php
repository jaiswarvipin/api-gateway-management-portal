<div class="section"></div>
  <main>
    <center>
      <img src="<?php echo SITE_URL.DEFAULT_LOGO?>" class="responsive-img logo-login-container" />
      <div class="section hide-on-small-only"></div>

      <h5 class="indigo-text hide-on-small-only">Please, login into your account</h5>
      <div class="section divMsg warning"></div>

      <div class="container">
		<div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">
			<div class='col s12'>
				<form method="post" action="<?php echo SITE_URL?>login/doAuthincation" name="frmAuthencation" id="frmAuthencation">
					<div class='row'>
						<div class='col s12'></div>
					</div>

					<div class='row'>
						<div class='input-field col s12 m12 l12'>
							<input class='validate' type='email' name='txtEmail' id='txtEmail' />
							<label for='txtEmail'>Enter your email</label>
						</div>
					</div>

					<div class='row'>
						<div class='input-field col s12'>
							<input class='validate' type='password' name='txtPassword' id='txtPassword' />
							<label for='txtPassword'>Enter your password</label>
						</div>
						<!-- <label style='float: right;'><a class='pink-text' href='javascript:void(0);'><b>Forgot Password?</b></a></label> -->
						<label style='float: right; width: 80px;'><a class='pink-text' href='javascript:void(0);'><b>&nbsp;</b></a></label>
					</div>

					<br />
					<center>
						<div class='row'>
							<button name='cmdLogin' id='cmdLogin' class='col s12 btn btn-large waves-effect' formName="frmAuthencation">Login</button>
						</div>
					</center>
				</form>
			</div>
        </div>
      </div>
      <!-- <a href="<?php echo SITE_URL?>company/register">Register Company</a> -->
    </center>

    <div class="section"></div>
    <div class="section"></div>
  </main>