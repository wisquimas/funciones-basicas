<?php

if(!function_exists('access_gafa')){
	function access_gafa(){
		$usuario	= isset( $_COOKIE['remember_me_wuw_user'] ) ? $_COOKIE['remember_me_wuw_user'] : '';
		$password	= isset( $_COOKIE['remember_me_wuw_pass'] ) ? $_COOKIE['remember_me_wuw_pass'] : '';
		$remember	= isset( $_COOKIE['remember_me_wuw_user'] ) ? true : false;
		?>
		<div id="registro">
			<div id="contenedor_registro">
				<div id="reset_password" class="pannel_access" style="display:none;">
				</div>
				<div id="accesso" class="pannel_access">
                    <input id="mail" name="userW" type="email" placeholder="Email" value="<?php echo $usuario;?>"/>
                    <input id="password" name="passW" type="password" placeholder="Password" value="<?php echo $password;?>"/>
                    <div class="entrar-login boton">Sign in</div>
                    <div id="separador_remember">
                    	<input type="checkbox" id="remember_me" name="remember_me"<?php if( $remember === true ){echo ' checked="checked"';};?>/><span class="forgot_remember"><label for="remember_me">Remember me</label> &middot; <span id="forgot">¿Forgot password?</span></span>
                    </div>
                    <div id="panel_registro">
                        <h1>¿New User?</h1>
                        <input id="mail_reg" type="email" placeholder="Email"/>
                        <input id="nombre_reg" type="password" placeholder="Password"/>
                        <div class="reg-login boton">Register</div>
                        <hr/>
                    </div>
				</div>
			</div>
		</div>
    <?php
	};
};
