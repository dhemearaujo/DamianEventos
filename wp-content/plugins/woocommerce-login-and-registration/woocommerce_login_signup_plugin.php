<?php
/*
Plugin Name: Woocommerce Login / Signup Lite
Plugin URI: https://www.phoeniixx.com/product/woocommerce-login-signup/
Description: With this free Sign Up/ Login plugin, you can easily create a sign up and login process for your ecommerce site.
Author: phoeniixx
Author URI: http://phoeniixx.com/
Version: 1.6.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: phoen_login_signup
WC requires at least: 2.6.0
WC tested up to: 3.4.4
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

ob_start();

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
{

	include(dirname(__FILE__).'/libs/execute-libs.php');

	function add_login_shortcode() 
	{
		ob_start();
		
		if ( !is_user_logged_in() ) 
		{
			echo'<div class="woocommerce">';
			
			if(isset($_POST['login']) && sanitize_text_field( $_POST['login'] ) ) 
			{
				
				global $wpdb;
				
				$nonce_check = isset($_POST['_wpnonce_phoe_login_form'])?sanitize_text_field( $_POST['_wpnonce_phoe_login_form'] ):"";
	
				if ( ! wp_verify_nonce( $nonce_check, 'phoe_login_form' ) ) 
				{
					
					die(   'Security check failed'  ); 
					
				}
				
				$username = isset($_POST['username'])?$wpdb->escape(sanitize_text_field($_POST['username'])):'';
				
				$password = isset($_POST['password'])?$_POST['password']:'';
				
				$remember = isset($_POST['rememberme'])?$wpdb->escape(sanitize_text_field($_POST['rememberme'])):'';
				
				$remember = ( $remember ) ? 'true' : 'false';
				
				if($username == '')
				{
					
						echo '<ul class="woocommerce-error">
									
									<li><strong>Error:</strong> Username is required.</li>
								
							</ul>';
							
				}
				else if($password == '')
				{
					
					echo '<ul class="woocommerce-error">
								
								<li><strong>Error:</strong> Password is required.</li>
						
						</ul>';
						
				}
				else
				{
					
					if(is_email($username)) 
					{
						
						$user= get_user_by('email',$username);
						
						if($user)
						{
							
							if(wp_check_password( $password, $user->user_pass))
							{
								
								wp_set_current_user( $user->ID, $user->user_login );
								
								wp_set_auth_cookie( $user->ID );
								
								do_action( 'wp_login', $user->user_login ,$user);
								
								$location = home_url()."/my-account/";
								
								// wp_redirect( $location );
								
								exit;
								
							}
							else
							{
								
								echo '<ul class="woocommerce-error">
										
											<li><strong>ERROR</strong>: The password you entered for the username <strong>'.$user->user_login.'</strong> is incorrect. 		
										
											<a href="'.get_site_url().'/my-account/lost-password/">Lost your password?</a></li>
									 
									</ul>';
							
							}
							
						}
						else
						{
							
							echo '<ul class="woocommerce-error">
									
									<li><strong>Error:</strong> A user could not be found with this email address.</li>
								  
								 </ul>';
								 
						}
						
					}
					else
					{
						
						$login_data = array();

						$login_data['user_login'] = $username;

						$login_data['user_password'] = $password;

						$login_data['remember'] = $remember;
		
						$user_verify = wp_signon($login_data,false);  
						
						if(is_wp_error($user_verify))
						{
								
								echo '<ul class="woocommerce-error">
				
											<li>'.$user_verify->get_error_message().'</li>
									  
									  </ul>';       
									  
						}
						else 
						{ 
							wp_set_current_user( $user_verify->ID, $user_verify->user_login );
						
							wp_set_auth_cookie( $user_verify->ID );
							
							do_action( 'wp_login', $user_verify->user_login, $user_verify);
							
							$location = home_url();  
							
							wp_redirect( $location );
							
						} 
						
					}   
					
				}
				
			}
				?>        
							
			<div class="col-set" id="customer_login">
			
				<div class="col">
				
					<h2>Login</h2>
					
					<form method="post" class="login">
					
					<?php $nonce = wp_create_nonce( 'phoe_login_form' ); ?>
							
						<input type="hidden" value="<?php echo $nonce; ?>" name="_wpnonce_phoe_login_form" id="_wpnonce_phoe_login_form" />

						<p class="form-row form-row-wide">
							<label for="username">Username or email address <span class="required">*</span></label>
							<input type="text" class="input-text" name="username" id="username" value="<?php echo isset( $username ) ? $username: '' ; ?>">
						</p>
						<p class="form-row form-row-wide">
							<label for="password">Password <span class="required">*</span></label>
							<input class="input-text" type="password" name="password" id="password">
						</p>
						<p class="form-row">
							<input type="hidden" id="_wpnonce" name="_wpnonce" value="fd684f83cf">
							<input type="hidden" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/">				
							<input type="submit" class="button" id="login" name="login" value="Login">
							<label for="rememberme" class="inline">
								<input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember me </label>
						</p>
						<p class="lost_password">
							<a href="<?php echo get_site_url(); ?>/my-account/lost-password/">Lost your password?</a>
						</p>
					</form>
				</div>
			</div>
			</div>        
			<?php  
		}
		/* else
		{
			  
			$location = get_home_url();
			
			 wp_redirect( $location);
			  
		} */
		
		return ob_get_clean();
	
	}
	
	
    function add_signup_shortcode()
	{ 
	
		ob_start();
		
		if ( !is_user_logged_in() ) 
		{ 
			
			echo ' <div class="woocommerce">';
			
			if(isset($_POST['register']) && sanitize_text_field ($_POST['register'] ) )
			{
		
				$nonce_check = isset($_POST['_wpnonce_phoe_register_form'])?sanitize_text_field( $_POST['_wpnonce_phoe_register_form'] ):'';

				if ( ! wp_verify_nonce( $nonce_check, 'phoe_register_form' ) ) 
				{
					
					die(   'Security check failed'  ); 
					
				}
				
				$reg_email = isset($_POST['email'])?sanitize_email($_POST['email']):'';
				
				$reg_password = isset($_POST['password'])? sanitize_text_field($_POST['password']):'';
				
				$arr_name = explode("@",$reg_email);  
				
				$temp = $arr_name[0];
				
				$user = get_user_by( 'email',$reg_email );			   
			    
				if($reg_email == '')
				{
					
					echo '<ul class="woocommerce-error">
					
							<li><strong>Error:</strong> Please provide a valid email address.</li>
							
						  </ul>';
			    
				}
				
				else if($reg_password == '')
				{
				
					echo '<ul class="woocommerce-error">
					
							<li><strong>Error:</strong> Please enter an account password.</li>
							
					      </ul>';
			    }
				else
				{
					
					if(is_email($reg_email))
					{ 	
						
						if($user->user_email == $reg_email)
						{
						
							echo'<ul class="woocommerce-error">
									
									<li><strong>Error:</strong> An account is already registered with your email address. Please login.</li>
								 
								 </ul>';
						}
					    else
						{             
							
							if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && empty( $reg_password ) ) {
								
									$reg_password = wp_generate_password();
									
									$password_generated = true;

								} elseif ( empty( $reg_password ) ) {
									
									return new WP_Error( 'registration-error-missing-password', __( 'Please enter an account password.', 'woocommerce' ) );

								} else {
									
									$password_generated = false;
									
								}
								
							$userdata=array("role"=>"customer",
							
											"user_email"=>$reg_email,
											
											"user_login"=>$temp,
											
											"user_pass"=>$reg_password);
							
							if($user_id = wp_insert_user( $userdata ))
							{ 
								
								do_action('woocommerce_created_customer', $user_id, $userdata, $password_generated);
								
								$user1 = get_user_by('id',$user_id);
							    
								wp_set_current_user( $user1->ID, $user1->user_login );
											   
							    wp_set_auth_cookie( $user1->ID );
							   
							    do_action( 'wp_login', $user1->user_login,$user1 );
							   
							   $location = home_url()."/my-account/"; 
							
							    // wp_redirect( $location );
								
							    exit;												 
							}
							
						}
						
					}
					else
					{
						echo '<ul class="woocommerce-error">
							
									<li><strong>Error:</strong> Please provide a valid email address.</li>
							
							</ul>';
							
					} 
					
				}
				
			}
			
?>        
	
			<div class="col-set" id="customer_login">
				<div class="col">
					<h2>Register</h2>
					<form method="post" class="register">	

						<?php $nonce_register = wp_create_nonce( 'phoe_register_form' ); ?>
							
						<input type="hidden" value="<?php echo $nonce_register; ?>" name="_wpnonce_phoe_register_form" id="_wpnonce_phoe_register_form" />
					
						<p class="form-row form-row-wide">
							<label for="reg_email">Email address <span class="required">*</span></label>
							<input type="email" class="input-text" name="email" id="reg_email" value="<?php echo isset( $reg_email ) ? $reg_email: '' ; ?>" >
						</p>			
							<p class="form-row form-row-wide">
								<label for="reg_password">Password <span class="required">*</span></label>
								<input type="password" class="input-text" name="password" id="reg_password " >
							</p>			
						<div style="left: -999em; position: absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" tabindex="-1"></div>						
						<p class="form-row">
							<input type="hidden" id="_wpnonce" name="_wpnonce" value="70c2c9e9dd"><input type="hidden" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/">				
							
							<input type="submit" class="button" name="register" value="Register">
						</p>
					</form>
				</div>
			</div>
		</div>
		
<?php        

		}
		/* else
		{
			
			$location = get_home_url();
			
			wp_redirect( $location);
			
	    } */
		return ob_get_clean();
	} // end of add_signup_shortcode().
	   
      // header short code area start(login):
	
	add_action( 'wp_ajax_val_header', 'header_validate' );
	
	add_action( 'wp_ajax_nopriv_val_header', 'header_validate' );
	
	function header_validate()
	{
		
		$nonce_check = isset($_POST['wpnonce'])?sanitize_text_field( $_POST['wpnonce'] ):'';

		/* if ( ! wp_verify_nonce( $nonce_check, 'phoe_login_pop_form' ) ) 
		{
			
			die(   '<ul class="woocommerce-error">
					
					<li><strong>Security check failed.</strong></li>
				  
				  </ul>'  ); 
			
		}
		 */
		if ( !is_user_logged_in() ) 
		{  
			
			global $wpdb;
									
			$username = isset($_POST['username'])?$wpdb->escape(sanitize_text_field($_POST['username'])):'';
			
			$password = isset($_POST['password'])?$_POST['password']:'';
			
			$remember = isset($_POST['rememberme'])?$wpdb->escape(sanitize_text_field($_POST['rememberme'])):'';
			
			if($remember) $remember = "true";
			
			else $remember = "false";
			
			if($username == '')
			{
				
				echo '<ul class="woocommerce-error">
						
						<li><strong>Error:</strong> Username is required.</li>
					  
					  </ul>';
					  
			}
			else if($password == '')
			{
				
				echo '<ul class="woocommerce-error">
						
						<li><strong>Error:</strong> Password is required.</li>
					  
					  </ul>';
					  
			}
			else
			{				
					
					if(is_email($username))
					{
						
						$user= get_user_by('email',$username);
						
						if($user)
						{
							
							if(wp_check_password( $password, $user->user_pass))
							{
							   
							   echo "1";	
							    
							   wp_set_current_user( $user->ID, $user->user_login );
							   
							   wp_set_auth_cookie( $user->ID );
							   
							   do_action( 'wp_login', $user->user_login,$user );
							   
							   exit;
							   
							}
							else
							{
								
								echo '<ul class="woocommerce-error">
									
										<li><strong>ERROR</strong>: The password you entered for the username <strong>'.$user->user_login.'</strong> is incorrect. 
									  
										 <a href="'.get_site_url().'/my-account/lost-password/">Lost your password?</a></li>
									 
									</ul>';
							
							}	
							
						}
						else
						{
							
							echo '<ul class="woocommerce-error">
							
										<li><strong>Error:</strong> A user could not be found with this email address.</li>
								  
								 </ul>';
								 
						}						
						
						}
						else
						{
						
							$login_data = array();
							
							$login_data['user_login'] = $username;
							
							$login_data['user_password'] = $password;
							
							$login_data['remember'] = $remember;
							
							$user_verify = wp_signon($login_data,false);  
							 
							if (is_wp_error($user_verify))
							{
								
								echo '<ul class="woocommerce-error">
								
											<li>'.$user_verify->get_error_message().'</li>
									 
									</ul>';                        
							
							}
							else
							{ 

								echo "1";
							  
								wp_set_current_user( $user_verify->ID, $user_verify->user_login );
							    
								wp_set_auth_cookie( $user_verify->ID );
							    
								do_action( 'wp_login', $user_verify->user_login ,$user);
							    
							    exit;
							
							} 
						
						}      
			
			}

			exit;
        
		}
		else
		{
			
			echo '<ul class="woocommerce-error">
                     
					 <li><strong>Error:</strong> A user already loged in, Logout First.</li>
                  
				 </ul>';
				 
		}
		
		exit;
    
	}   // end of header_validate	 
	    // header short code area end(login)
		// header short code area start(signup):
		 
	add_action( 'wp_ajax_val_header_signup', 'header_validate_signup' );
	
	add_action( 'wp_ajax_nopriv_val_header_signup', 'header_validate_signup' );
    
	function header_validate_signup()
	{
		
		$nonce_check = isset($_POST['wpnonce'])?sanitize_text_field( $_POST['wpnonce'] ):'';

		/* if ( ! wp_verify_nonce( $nonce_check, 'phoe_register_pop_form' ) ) 
		{
			
			die(   '<ul class="woocommerce-error">
					
					<li><strong>Security check failed.</strong></li>
				  
				  </ul>'  ); 
			
		} */
			
		if (!is_user_logged_in())
		{ 
	
			$reg_email = isset($_POST['email'])?sanitize_text_field( $_POST['email'] ):'';
		    
			$reg_password =  isset($_POST['password'])?sanitize_text_field( $_POST['password'] ):'';
		    
			$arr_name = explode("@",$reg_email);  $temp = $arr_name[0];
		    
			$user = get_user_by( 'email',$reg_email );
		   
		    if($reg_email == '')
			{
				
				echo '<ul class="woocommerce-error">
						
						<li><strong>Error:</strong> Please provide a valid email address.</li>
					  
					</ul>';
					
		    }
			
			else if($reg_password == '')
			{
				
				echo '<ul class="woocommerce-error">
			    
						<li><strong>Error:</strong> Please enter an account password.</li>
				      
					 </ul>';
					 
		    }
			else
			{
			   
				if(is_email($reg_email))
				{ 
					
					if($user->user_email == $reg_email)
					{
						
						echo'<ul class="woocommerce-error">
								
								<li><strong>Error:</strong> An account is already registered with your email address. Please login.</li>
							 
							</ul>';
					
					}
					else
					{             
						
						if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && empty( $reg_password ) ) {
									$reg_password = wp_generate_password();
									$password_generated = true;

								} elseif ( empty( $reg_password ) ) {
									return new WP_Error( 'registration-error-missing-password', __( 'Please enter an account password.', 'woocommerce' ) );

								} else {
								$password_generated = false;
							}
								
						$userdata=array("role"=>"customer",
						
									"user_email"=>$reg_email,
									
									"user_login"=>$temp,
									
									"user_pass"=>$reg_password);
						
						if($user_id = wp_insert_user( $userdata ))
						{
							
							echo "1";
							
							do_action('woocommerce_created_customer', $user_id, $userdata, $password_generated);
							
							$user1 = get_user_by('id',$user_id);
							
							wp_set_current_user( $user1->ID, $user1->user_login );
							
							wp_set_auth_cookie( $user1->ID );
							
							do_action( 'wp_login', $user1->user_login ,$user1);
							
							exit;
											 
						}
						
					}
			    
				}
				else
				{
					
					echo '<ul class="woocommerce-error">
						
							<li><strong>Error:</strong> Please provide a valid email address.</li>
						
						</ul>';
				
				} 
			
			}		
			
			exit;
		}
		else
		{
			
			echo '<ul class="woocommerce-error">
			
						<li><strong>Error:</strong> A user already loged in, Logout First.</li>
				  
				  </ul>';
		
		}			
		
	die();	
	}// header short code area  end(signup)
   
    function add_header_shortcode()
	{
        ob_start();
		
		if (!is_user_logged_in())
		{ 
			
			// ajax call start
			
			wp_enqueue_script("login-signup-js",plugins_url( '' , __FILE__ )."/assets/js/custom.js",array('jquery'),'',true);
			
			wp_localize_script( 'login-signup-js', 'woo_log_ajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
			//end of ajax call

			wp_enqueue_script("jquery.colorbox-js",plugins_url( '' , __FILE__ )."/assets/js/jquery.colorbox.js",array('jquery'),'',true);
			
			wp_enqueue_style( 'style-colorbox', plugins_url( '' , __FILE__ ).'/assets/css/colorbox.css' );
            
			if(get_option("popup_status") == 'on')
			{
			
				echo '<p><a href="#" class="header_login" >Login</a><a href="#" class="header_signup"> Sign Up</a> ';
				
				?>
				
				<div style="display: none;">
				
					<?php          
				
						echo '<div id="login_data">';          
						
						echo'<div class="woocommerce">';        
						
						?> 
						
						<div class="col-set" id="customer_login" >
						
							<div class="col" >
							
							<div class="result1"></div> 
							
								<h2>Login</h2>

								<form method="post" class="login" id="js_login">
								
								<?php $nonce_login_pop = wp_create_nonce( 'phoe_login_pop_form' ); ?>
									
									<input type="hidden" value="<?php echo $nonce_login_pop; ?>" name="_wpnonce_phoe_login_pop_form" id="wpnonce_phoe_login_pop_form" />
							
									<p class="form-row form-row-wide">
										<label for="username">Username or email address <span class="required">*</span></label>
										<input type="text" class="input-text" name="username" id="username" value="">
									</p>
									<p class="form-row form-row-wide">
										<label for="password">Password <span class="required">*</span></label>
										<input class="input-text" type="password" name="password" id="password">
									</p>
									<p class="form-row">
										<input type="hidden" id="_wpnonce" name="_wpnonce" value="fd684f83cf">
										<input type="hidden" id="wp_http_referer1" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/"><div class="loader1" style="display:none;" ><img src="<?php echo plugins_url( '' , __FILE__ )."/assets/img/ajax-loader.gif" ?>"/></div>				
										<input type="submit" class="button js_login_log" name="login" value="Login" id="login1">
										<label for="rememberme" class="inline">
										<input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember me </label>
									</p>
								<p class="lost_password">
									<a href="<?php echo get_site_url(); ?>/my-account/lost-password/">Lost your password?</a>
								</p>
							</form>
						</div>
					</div> 
					</div>  
					</div>  <!-- end of login data -->
				   
					<div id="signup_data">
					
					<?php       
			 
					echo ' <div class="woocommerce">';
				 
					?>        
				
		   
						<div class="col-set" id="customer_login">
						<div class="col" >
							<div class="result2"></div>
							<h2>Register</h2>
							<form method="post" class="register" id="js_signup" >			

								<?php $nonce_register_pop = wp_create_nonce( 'phoe_register_pop_form' ); ?>
									
									<input type="hidden" value="<?php echo $nonce_register_pop; ?>" name="_wpnonce_phoe_register_pop_form" id="wpnonce_phoe_register_pop_form" />
												
								<p class="form-row form-row-wide">
									<label for="reg_email">Email address <span class="required">*</span></label>
									<input type="email" class="input-text" name="email" id="reg_email_header" value="" >
								</p>			
									<p class="form-row form-row-wide">
										<label for="reg_password">Password <span class="required">*</span></label>
										<input type="password" class="input-text" name="password" id="reg_password_header" >
									</p>			
								<!-- Spam Trap -->
								<div style="left: -999em; position: absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" tabindex="-1"></div>						
								<p class="form-row">
									<input type="hidden" id="_wpnonce" name="_wpnonce" value="70c2c9e9dd"><input id="wp_http_referer" type="hidden" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/">				
									<div class="loader_reg" style="display:none;" ><img src="<?php echo plugins_url( '' , __FILE__ )."/assets/img/ajax-loader.gif" ?>"/></div>				
									<input type="submit" class="button phoen_reg" name="register_header" value="Register">
								</p>
							</form>
						</div>
					</div>
					</div>
					</div> <!-- end of signup data -->
					<?php 
					}
					else
					{
						
						echo '<p><a href="'.get_option("login_url").'"  >Login </a><a href="'.get_option("signup_url").'">Sign Up</a> ';
						 
					} 
					?> 
				</div>
       
		<?php
         
		}
		else
		{
			
			$user_obj = wp_get_current_user();
			 			 
?>

			<p><span class="phoe-span-1">Hello</span> <strong><?php echo $user_obj->user_login; ?></strong> <span class="phoe-span-2">(not <?php echo $user_obj->user_login; ?> </span> 
			  <a href="<?php echo wp_logout_url( get_permalink() );  ?>">Sign out</a> <span class="phoe-span-3">). </span>
			</p>

<?php	
				
		}
		return ob_get_clean();
	}
	
	
	function phoen_header_login()
	{
        ob_start();
		
		if (!is_user_logged_in())
		{ 
			 
			// ajax call start
			
			wp_enqueue_script("login-signup-js",plugins_url( '' , __FILE__ )."/assets/js/custom.js",array('jquery'),'',true);
			
			wp_localize_script( 'login-signup-js', 'woo_log_ajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
			//end of ajax call

			wp_enqueue_script("jquery.colorbox-js",plugins_url( '' , __FILE__ )."/assets/js/jquery.colorbox.js",array('jquery'),'',true);
			
			wp_enqueue_style( 'style-colorbox', plugins_url( '' , __FILE__ ).'/assets/css/colorbox.css' );
            
			/* if(get_option("popup_status") == 'on')
			{ */
			
				//echo '<p><a href="#" class="header_login" >Login</a><a href="#" class="header_signup"> Sign Up</a> ';
				
				?>
				
				
				<div style="display: none;">
				
					<?php          
				
					echo '<div id="phoen_login_data">';          
						
						echo'<div class="woocommerce">';        
						
						?> 
						
					<div class="col-set" id="customer_login">
						
						<div class="col" >
							
							<div class="result1"></div> 
							
								<h2>Login</h2>

								<form method="post" class="login" id="js_login">
								
								<?php $nonce_login_pop = wp_create_nonce( 'phoe_login_pop_form' ); ?>
									
									<input type="hidden" value="<?php echo $nonce_login_pop; ?>" name="_wpnonce_phoe_login_pop_form" id="wpnonce_phoe_login_pop_form" />
							
									<p class="form-row form-row-wide">
										<label for="username">Username or email address <span class="required">*</span></label>
										<input type="text" class="input-text" name="username" id="username" value="">
									</p>
									<p class="form-row form-row-wide">
										<label for="password">Password <span class="required">*</span></label>
										<input class="input-text" type="password" name="password" id="password">
									</p>
									<p class="form-row">
										<input type="hidden" id="_wpnonce" name="_wpnonce" value="fd684f83cf">
										<input type="hidden" id="wp_http_referer1" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/"><div class="loader1" style="display:none;" ><img src="<?php echo plugins_url( '' , __FILE__ )."/assets/img/ajax-loader.gif" ?>"/></div>				
										<input type="submit" class="button js_login_log" name="login" value="Login" id="login1">
										<label for="rememberme" class="inline">
										<input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember me </label>
									</p>
								<p class="lost_password">
									<a href="<?php echo get_site_url(); ?>/my-account/lost-password/">Lost your password?</a>
								</p>
								<p>
									<a href="#" class="phoen_signup"> Sign Up</a>
								</p>
							</form>
						</div>
						
					</div> 
					</div>  
					</div>  <!-- end of login data -->
					
					<div id="phoen_signup_data">
					
					<?php       
			 
					echo ' <div class="woocommerce">';
				 
					?>        
				
					<div class="col-set" id="customer_login">
						<div class="col" >
							<div class="result2"></div>
							<h2>Register</h2>
							<form method="post" class="register" id="js_signup" >			

								<?php $nonce_register_pop = wp_create_nonce( 'phoe_register_pop_form' ); ?>
									
									<input type="hidden" value="<?php echo $nonce_register_pop; ?>" name="_wpnonce_phoe_register_pop_form" id="wpnonce_phoe_register_pop_form" />
												
								<p class="form-row form-row-wide">
									<label for="reg_email">Email address <span class="required">*</span></label>
									<input type="email" class="input-text" name="email" id="reg_email_header" value="" >
								</p>			
									<p class="form-row form-row-wide">
										<label for="reg_password">Password <span class="required">*</span></label>
										<input type="password" class="input-text" name="password" id="reg_password_header" >
									</p>			
								<!-- Spam Trap -->
								<div style="left: -999em; position: absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" tabindex="-1"></div>						
								<p class="form-row">
									<input type="hidden" id="_wpnonce" name="_wpnonce" value="70c2c9e9dd"><input id="wp_http_referer" type="hidden" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/">				
									<div class="loader_reg" style="display:none;" ><img src="<?php echo plugins_url( '' , __FILE__ )."/assets/img/ajax-loader.gif" ?>"/></div>				
									<input type="submit" class="button phoen_reg" name="register_header" value="Register">
								</p>
								<p><a href="#" class="phoen_login" >Login</a>
							</form>
						</div>
					
					</div>
					</div>
					</div> <!-- end of signup data -->
					
				</div>	
				
				
				<div style="display: none;">
				
					<?php          
				
					echo '<div id="phoen_login_data_val">';          
						
						echo'<div class="woocommerce">';        
						
						?> 
						
					<div class="col-set" id="customer_login" >
						
						<div class="col" >
							
							<div class="result1"></div> 
							
								<h2>Login</h2>

								<form method="post" class="login" id="js_login">
								
								<?php $nonce_login_pop = wp_create_nonce( 'phoe_login_pop_form' ); ?>
									
									<input type="hidden" value="<?php echo $nonce_login_pop; ?>" name="_wpnonce_phoe_login_pop_form" id="wpnonce_phoe_login_pop_form" />
							
									<p class="form-row form-row-wide">
										<label for="username">Username or email address <span class="required">*</span></label>
										<input type="text" class="input-text" name="username" id="username" value="">
									</p>
									<p class="form-row form-row-wide">
										<label for="password">Password <span class="required">*</span></label>
										<input class="input-text" type="password" name="password" id="password">
									</p>
									<p class="form-row">
										<input type="hidden" id="_wpnonce" name="_wpnonce" value="fd684f83cf">
										<input type="hidden" id="wp_http_referer1" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/"><div class="loader1" style="display:none;" ><img src="<?php echo plugins_url( '' , __FILE__ )."/assets/img/ajax-loader.gif" ?>"/></div>				
										<input type="submit" class="button js_login_log" name="login" value="Login" id="login1">
										<label for="rememberme" class="inline">
										<input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember me </label>
									</p>
								<p class="lost_password">
									<a href="<?php echo get_site_url(); ?>/my-account/lost-password/">Lost your password?</a>
								</p>
							</form>
						</div>
						
					</div> 
					</div>  
					</div>  <!-- end of login data -->
				   
					<div id="phoen_signup_data_val">
					
					<?php       
			 
					echo ' <div class="woocommerce">';
				 
					?>        
				
					<div class="col-set" id="customer_login">
						<div class="col" >
							<div class="result2"></div>
							<h2>Register</h2>
							<form method="post" class="register" id="js_signup" >			

								<?php $nonce_register_pop = wp_create_nonce( 'phoe_register_pop_form' ); ?>
									
									<input type="hidden" value="<?php echo $nonce_register_pop; ?>" name="_wpnonce_phoe_register_pop_form" id="wpnonce_phoe_register_pop_form" />
												
								<p class="form-row form-row-wide">
									<label for="reg_email">Email address <span class="required">*</span></label>
									<input type="email" class="input-text" name="email" id="reg_email_header" value="" >
								</p>			
									<p class="form-row form-row-wide">
										<label for="reg_password">Password <span class="required">*</span></label>
										<input type="password" class="input-text" name="password" id="reg_password_header" >
									</p>			
								<!-- Spam Trap -->
								<div style="left: -999em; position: absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" tabindex="-1"></div>						
								<p class="form-row">
									<input type="hidden" id="_wpnonce" name="_wpnonce" value="70c2c9e9dd"><input id="wp_http_referer" type="hidden" name="_wp_http_referer" value="<?php echo get_site_url(); ?>/my-account/">				
									<div class="loader_reg" style="display:none;" ><img src="<?php echo plugins_url( '' , __FILE__ )."/assets/img/ajax-loader.gif" ?>"/></div>				
									<input type="submit" class="button phoen_reg" name="register_header" value="Register">
								</p>
							</form>
						</div>
					
					</div>
					</div>
					</div> <!-- end of signup data -->
				</div>
				
		<?php
		}else{
			
				if (is_user_logged_in())
				{ 
			
				wp_enqueue_script("login-signup-js",plugins_url( '' , __FILE__ )."/assets/js/custom2.js",array('jquery'),'',true);
				
				//wp_localize_script( 'login-signup-js', 'woo_log_ajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
				
				//end of ajax call

				wp_enqueue_script("jquery.colorbox-js",plugins_url( '' , __FILE__ )."/assets/js/jquery.colorbox.js",array('jquery'),'',true);
				
				wp_enqueue_style( 'style-colorbox', plugins_url( '' , __FILE__ ).'/assets/css/colorbox.css' );
			
			?>
			<div style="display: none;">
				<div id="phoen_myaccount_login">
					
					<?php       
			 
					echo ' <div class="woocommerce">';
				 
					?>        
				
					<div class="col-set" id="customer_login">
						<div class="col" >
							<div class="result2"></div>
							<h2>Navigation</h2>
								<nav class="woocommerce-MyAccount-navigation">
								<ul>
									<?php foreach ( wc_get_account_menu_items() as $phoen_menu_myaccount => $phoen_menu_myaccount_data ) : 
									if($phoen_menu_myaccount_data!='')
									{
									?>
										<li class="<?php echo wc_get_account_menu_item_classes( $phoen_menu_myaccount ); ?>">
											<a href="<?php echo esc_url( wc_get_account_endpoint_url( $phoen_menu_myaccount ) ); ?>"><?php echo esc_html( $phoen_menu_myaccount_data ); ?></a>
										</li>
									<?php
									}
									endforeach; ?>
								</ul>
							</nav>
						</div>
					
					</div>
					</div>
				</div> <!-- end of signup data -->
			</div>
	
			<?php
			
				}	
		} 
         
	}
	
	
function phoen_my_media_lib_uploader_enqueue() {
    wp_enqueue_media();
  //  wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
    wp_enqueue_script( 'media-lib-uploader-js' );
  }
  add_action('admin_enqueue_scripts', 'phoen_my_media_lib_uploader_enqueue');
	
	
	add_shortcode("lsphe-login-form","add_login_shortcode");
       
	add_shortcode("lsphe-signup-form","add_signup_shortcode");
    
	add_shortcode("lsphe-header","add_header_shortcode");
	
	add_shortcode("wp-login-form","add_login_shortcode");
       
	add_shortcode("wp-signup-form","add_signup_shortcode");
    
	add_shortcode("wp-header","add_header_shortcode");
	
    add_filter( 'widget_text', 'shortcode_unautop');
	
	add_filter('widget_text', 'do_shortcode');
	
	add_action('wp_head','phoen_header_login');
	
	function ph_login_signup_add_menu()
	{
		
		$page_title='Login/Signup Setting';
		
		$menu_title='Login/Signup';
		
		$capability='manage_options';
		
		$menu_slug='login_signup_settings';
		
		$function='settings_wp_login_signup';
		
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug,$function , $icon_url, $position );
		
		add_menu_page( 'phoeniixx', __( 'Phoeniixx', 'phe' ), 'nosuchcapability', 'phoeniixx', NULL, plugin_dir_url( __FILE__ ).'assets/img/logo-wp.png', 57 );
        
		add_submenu_page( 'phoeniixx', $page_title, $menu_title, $capability, $menu_slug, $function );
	

	}
	
	
	
	function ph_login_signup_activate() {

		if(get_option("popup_status") == ""){
			update_option("popup_status","on");
		}
	
	}
	register_activation_hook( __FILE__, 'ph_login_signup_activate' );
	
    add_action("admin_menu","ph_login_signup_add_menu",99);
	
    function settings_wp_login_signup()
	{ 
        
		if( !empty( $_GET['tab'] ) )
		{
			
			$tab = sanitize_text_field( $_GET['tab'] );
			
		}
		else
		{
			$tab = '';
		}
		
		
		echo "<h3>Plugin Settings</h3>"; 
		
		?>
		
		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				
			<a class="nav-tab <?php if($tab == 'general' || $tab == ''){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=login_signup_settings&amp;tab=general">General</a>
			
			<a class="nav-tab <?php if($tab == 'allp'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=login_signup_settings&amp;tab=allp">Premium</a>
			
		</h2>
		
	<?php 
	
    $plugin_dir_url =  plugin_dir_url( __FILE__ );
    
	if($tab == 'general' || $tab == '')
	{
		
        if( !empty( $_POST['submit_1'] ) && sanitize_text_field( $_POST['submit_1'] ) && current_user_can( 'manage_options' )  )
		{
			
			$nonce_check = sanitize_text_field( $_POST['_wpnonce_login_signup_setting'] );
	
			if ( ! wp_verify_nonce( $nonce_check, 'login_signup_setting' ) ) 
			{
				
				die(  'Security check failed'  ); 
				
			}
			else 
			{
				
				if( !empty( $_POST['popup'] ) )
				{
					
					$popup = sanitize_text_field($_POST['popup']);
				
				}
				else
				{
					
					$popup = '';
					
				}
				
				if( !empty( $_POST['login_page'] ) )
				{
					
					$login_url = sanitize_text_field($_POST['login_page']);
				
				}
				else
				{
					
					$login_url = '';
					
				}
            
				if( !empty( $_POST['signup_page'] ) )
				{
					
					$signup_url = sanitize_text_field($_POST['signup_page']);
				
				}
				else
				{
					
					$signup_url = '';
					
				}

				if($popup=='on')
				{
					
					$option="popup_status";
				
					$value="on";
					
					$autoload="yes";
					
					update_option($option, $value, $autoload );
					
					?>
	 
						<div class="updated notice is-dismissible below-h2" id="message"><p>Successfully saved. </p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>

					<?php
													
				}
				else if($popup!='on' && $login_url!='' && $signup_url !='' )
				{
					
					$option="popup_status";
					
					$value="off";
					
					$autoload="yes";
					
					update_option($option, $value, $autoload ); 
					
					$option="login_url";
					
					$value= $login_url;
					
					$autoload="yes";
					
					update_option($option, $value, $autoload ); 
					
					$option="signup_url";
					
					$value=$signup_url;
					
					$autoload="yes";
					
					update_option($option, $value, $autoload );              
					
					?> 

				   <div class="updated notice is-dismissible below-h2" id="message"><p>Successfully saved. </p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>

					<?php
							
				}
				else
				{
				   
					?>

						<div class="error notice is-dismissible below-h2" id="message"><p>Fields with * are mandatory, try again. </p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>

					<?php                

				}
				

			}
		
		}
		?>
		<div class="meta-box-sortables" id="normal-sortables">
				<div class="postbox " id="pho_wcpc_box">
					<h3><span class="upgrade-setting">Upgrade to the PREMIUM VERSION</span></h3>
					<div class="inside">
						<div class="pho_check_pin">

							<div class="column two">
								<!-- <h2>Get access to Pro Features</h2> -->

								<p>Switch to the premium version of Woocommerce Login Signup to get the benefit of all features!</p>

									<p>
										<a target="_blank" href="http://www.phoeniixx.com/product/woocommerce-login-signup/"><img src="<?php echo $plugin_dir_url; ?>images/premium-btn.png" /></a>
										<a target="blank" href="http://loginsignup.phoeniixxdemo.com/"><img src="<?php echo $plugin_dir_url; ?>images/demo-btns.png" /></a>
									</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="phoe_video_main">
				<h3>How to set up plugin</h3> 
				<iframe width="800" height="360"src="https://www.youtube.com/embed/kVkhbMP7Hjw" allowfullscreen></iframe>
			</div>
			
			<style>
			.postbox h3{padding:10px;}
			.phoe_video_main {
					padding: 20px;
					text-align: center;
				}
				
				.phoe_video_main h3 {
					color: #02c277;
					font-size: 28px;
					font-weight: bolder;
					margin: 20px 0;
					text-transform: capitalize
					display: inline-block;
				}
			</style>
        <?php if(get_option("popup_status")!= 'on')
		{  
	
			?>
    
			<div class="wrap" id="profile-page">
			
			<form action="" id="form7" method="post">
			
			<?php $nonce = wp_create_nonce( 'login_signup_setting' ); ?>
							
				<input type="hidden" value="<?php echo $nonce; ?>" name="_wpnonce_login_signup_setting" id="_wpnonce_login_signup_setting" />

				<table class="form-table">
				<tbody>		
				<tr class="user-nickname-wrap">
					Popup Enable :<td class="pho_enable"><input type="checkbox" id="popup1" name="popup"    /></td>
				</tr>
				
				<tr class="user-nickname-wrap">
				<td>
					<p>Popup Login Class: <code>phoen-login-popup-open</code></p>
				</td>
			</tr>	
			<tr class="user-nickname-wrap">
				<td>
				<p>Popup signup Class:  <code>phoen-signup-popup-open</code></p>
					
				</td>	
			</tr>	
			<tr class="user-nickname-wrap">			
				<td>
				<p>Popup For Both  Login & signup Class: <code>phoen-login-signup-popup-open</code></p>
				</td>	
			</tr>	
			
			<tr class="user-nickname-wrap">			
				<td>
				<p>Short code for login form –<code>[lsphe-login-form]</code></p>
				</td>	
			</tr>	
			
			<tr class="user-nickname-wrap">			
				<td>
				<p>Short code for signup form – <code>[lsphe-signup-form]</code></p>
				</td>	
			</tr>	
			
			<tr class="user-nickname-wrap">			
				<td>
				<p>Shortcode for both login and register form<code>[lsphe-header]</code></p>
				</td>	
			</tr>	
				
				
				<tr class="login user-nickname-wrap">
				<th><label>Login Page Slug :</label></th><td><?php //echo get_site_url()."/ "; ?><input id="log_url" type="text"  name="login_page" value="<?php //echo get_option("login_url"); ?>"   />*</td>
				
				</tr>
				
				<tr class="signup user-nickname-wrap">
					<th><label>Signup Page Slug :</label></th><td><?php //echo get_site_url()."/ "; ?><input id="sign_url" type="text"  name="signup_page" value="<?php //echo get_option("signup_url"); ?>"    />*</td>
				   
				</tr>
				
				<tr class="user-nickname-wrap">
				<td colspan="2"><input type="submit" class="button button-primary" id="submit1" name="submit_1" value="Save" /> </td>
				
				</tr>
			   </tbody>	
				</table>
				
			<!--<label><b>Popup Enable :</b></label> <input type="checkbox" id="popup1" name="popup"    />	
			
			<p>Popup Login Class <code>phoen-login-popup-open</code></p>
			
			<p>Popup signup Class  <code>phoen-signup-popup-open</code></p>
			
			<p>Popup For Both  Login & signup Class <code>phoen-login-signup-popup-open</code></p>
			
			<p>Short code for login form –<code>[lsphe-login-form]</code></p>
			
			<p>Short code for signup form – <code>[lsphe-signup-form]</code></p>
			
			<p>Shortcode for both login and register form<code>[lsphe-header]</code></p>
			
			<input type="submit" class="button button-primary" id="submit1" name="submit_1" value="Save" />-->
			</form>
			</div>
		   
			<?php

        }
		else
		{    
			?>  
        
			<style>
			.login{display: none;}
			.signup{display:none;}
			</style>

        
			<div class="wrap" id="profile-page">
			
			<form action="" id="form7" method="post">
			
			<?php $nonce = wp_create_nonce( 'login_signup_setting' ); ?>
							
				<input type="hidden" value="<?php echo $nonce; ?>" name="_wpnonce_login_signup_setting" id="_wpnonce_login_signup_setting" />

			<table class="form-table">
			<tbody>		
			<tr class="user-nickname-wrap">
				<td class="pho_enable">Popup Enable :<input type="checkbox" id="popup1" name="popup"  checked  /></td>
			</tr>
			
			
			<tr class="user-nickname-wrap">
				<td>
					<p>Popup Login Class: <code>phoen-login-popup-open</code></p>
				</td>
			</tr>	
			<tr class="user-nickname-wrap">
				<td>
				<p>Popup signup Class:  <code>phoen-signup-popup-open</code></p>
					
				</td>	
			</tr>	
			<tr class="user-nickname-wrap">			
				<td>
				<p>Popup For Both  Login & signup Class: <code>phoen-login-signup-popup-open</code></p>
				</td>	
			</tr>	
			
			<tr class="user-nickname-wrap">			
				<td>
				<p>Short code for login form –<code>[lsphe-login-form]</code></p>
				</td>	
			</tr>	
			
			<tr class="user-nickname-wrap">			
				<td>
				<p>Short code for signup form – <code>[lsphe-signup-form]</code></p>
				</td>	
			</tr>	
			
			<tr class="user-nickname-wrap">			
				<td>
				<p>Shortcode for both login and register form<code>[lsphe-header]</code></p>
				</td>	
			</tr>	
			
			
			<tr class="login user-nickname-wrap">
			<th><label>Login Page Slug :</label></th><td><?php echo get_site_url()."/ "; ?><input id="log_url" type="text"  name="login_page"   />*</td>
				
			</tr>
				
			<tr class="signup user-nickname-wrap">
				<th><label>Signup Page Slug :</label></th><td><?php echo get_site_url()."/ "; ?><input id="sign_url" type="text"  name="signup_page"    />*</td>
				
			</tr>
			
			<tr class="user-nickname-wrap">
			<td colspan="2"><input type="submit" class="button button-primary" id="submit1" name="submit_1" value="Save" /> </td>
			
			</tr>
		   </tbody>	
			</table>
			<!--<label><b>Popup Enable :</b></label>  <input type="checkbox" id="popup1" name="popup"  checked  />
			
			<p>Popup Login Class: <code>phoen-login-popup-open</code></p>
			
			<p>Popup signup Class:  <code>phoen-signup-popup-open</code></p>
			
			<p>Popup For Both  Login & signup Class: <code>phoen-login-signup-popup-open</code></p>
			
			<p>Short code for login form –<code>[lsphe-login-form]</code></p>
			
			<p>Short code for signup form – <code>[lsphe-signup-form]</code></p>
			
			<p>Shortcode for both login and register form<code>[lsphe-header]</code></p>
			
			<input type="submit" class="button button-primary" id="submit1" name="submit_1" value="Save" />-->
			</form>
			</div>    
			
			<?php      

        }
	
	}else if($tab == 'allp')
	{
		?>
		<style>
						 /*upgrade css*/

						 a:focus {
							 box-shadow: none;
						 }
						 
						.upgrade{background:#f4f4f9;padding: 50px 0; width:100%; clear: both;}
						.upgrade .upgrade-box{ background-color: #808a97;
							color: #fff;
							margin: 0 auto;
						   min-height: 110px;
							position: relative;
							width: 60%;}

						.upgrade .upgrade-box p{ font-size: 15px;
							 padding: 19px 20px;
							text-align: center;}

						.upgrade .upgrade-box a{background: none repeat scroll 0 0 #6cab3d;
							border-color: #ff643f;
							color: #fff;
							display: inline-block;
							font-size: 17px;
							left: 50%;
							margin-left: -150px;
							outline: medium none;
							padding: 11px 6px;
							position: absolute;
							text-align: center;
							text-decoration: none;
							top: 36%;
							width: 277px;}

						.upgrade .upgrade-box a:hover{background: none repeat scroll 0 0 #72b93c;}
                       
					   /**premium box**/    
						.premium-box{ width:100%; height:auto; background:#fff; float:left; }
						.premium-features{}
						.premium-heading{color:#484747;font-size: 40px; padding-top:35px;text-align:center;text-transform:uppercase;}
						.premium-features li{ width:100%; float:left;  padding: 80px 0; margin: 0; }
						.premium-features li .detail{ width:50%; }
						.premium-features li .img-box{ width:50%;box-sizing:border-box; }
						

						.premium-features li:nth-child(odd) { background:#f4f4f9; }
						.premium-features li:nth-child(odd) .detail{float:right; text-align:left; }
						.premium-features li:nth-child(odd) .detail .inner-detail{}
						.premium-features li:nth-child(odd) .detail p{ }
						.premium-features li:nth-child(odd) .img-box{ float:left; text-align:right; padding-right:30px;}

						.premium-features li:nth-child(even){  }
						.premium-features li:nth-child(even) .detail{ float:left; text-align:right;}
						.premium-features li:nth-child(even) .detail .inner-detail{ margin-right: 46px;}
						.premium-features li:nth-child(even) .detail p{ float:right;} 
						.premium-features li:nth-child(even) .img-box{ float:right;}

						.premium-features .detail{}
						.premium-features .detail h2{ color: #484747;  font-size: 24px; font-weight: 700; padding: 0; line-height:1.1;}
						.premium-features .detail p{  color: #484747;  font-size: 13px;  max-width: 327px;}
					 
					 /**images**/
					 
					 .pd_prm_option1 { background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option1.png") no-repeat; width:100%; max-width:496px; height:98px; display:inline-block; background-size:100% auto;}
					  
					 .prm_option2{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option2.png") no-repeat; width:100%;max-width:409px; height:82px; display:inline-block;  background-size:100% auto; }
					
                     .prm_option3{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option3.png") no-repeat; width:100%;max-width:452px;   height:118px; display:inline-block;background-size:100% auto;}

					 .prm_option4{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option4.png") no-repeat; width:100%;max-width:264px;  height:73px; display:inline-block;  background-size:100% auto;}					
					 .prm_option5{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option5.png") no-repeat; width:100%;max-width:658px; height:265px; display:inline-block; background-size:100% auto;}	
					 .prm_option6{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option6.png") no-repeat; width:100%; max-width:514px; height: 138px; display:inline-block; background-size:100% auto;}  					
					 .prm_option7{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option7.png") no-repeat; width:100%; max-width:670px; height: 864px; display:inline-block; background-size:100% auto;}  					
					 .prm_option8{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option8.png") no-repeat; width:100%; max-width:743px; height: 955px; display:inline-block; background-size:100% auto;}  					
					 .prm_option9{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option9.png") no-repeat; width:100%; max-width:689px; height: 346px; display:inline-block; background-size:100% auto;}  					
					 .prm_option10{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option10.png") no-repeat; width:100%; max-width:600px; height: 279px; display:inline-block; background-size:100% auto;}  					
				     .prm_option11{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option11.png") no-repeat; width:100%; max-width:395px; height: 462px; display:inline-block; background-size:100% auto;}
					 .prm_option12{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option12.png") no-repeat; width:100%; max-width:405px; height: 450px; display:inline-block; background-size:100% auto;}
					 .prm_option13{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option13.png") no-repeat; width:100%; max-width:323px; height: 70px; display:inline-block; background-size:100% auto;}					 
					 .prm_option14{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option14.png") no-repeat; width:100%; max-width:421px; height: 68px; display:inline-block; background-size:100% auto;}
					 .prm_option14_front{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option14-front.png") no-repeat; width:100%; max-width:270px; height: 133px; display:inline-block; background-size:100% auto;}
					 .prm_option15{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option15.png") no-repeat; width:100%; max-width:647px; height: 231px; display:inline-block; background-size:100% auto;}					 
					 .prm_option15_front{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>images/prm_option15-front.png") no-repeat; width:100%; max-width:790px; height: 654px; display:inline-block; background-size:100% auto;}
                     
   
.premium-box .premium-box-head {
    background: #eae8e7 none repeat scroll 0 0;
    height: 500px;
    text-align: center;
    width: 100%;
}
.premium-box .premium-box-head .pho-upgrade-btn {
    display: block;
    text-align: center;
}

.premium-box .premium-box-head .pho-upgrade-btn a {
    display: inline-block;
    margin-top: 75px;
}


.main-heading {
    background: #fff none repeat scroll 0 0;
    margin-bottom: -70px;
    text-align: center;
}
.main-heading img {
    margin-top: -200px;
}

.premium-box-container {
    margin: 0 auto;
}
.premium-box-container .description:nth-child(2n+1) {
    background: #fff none repeat scroll 0 0;
}
.premium-box-container .description {
    display: block;
    padding: 35px 0;
    text-align: center;
}

.premium-box-container .pho-desc-head::after {
    background: rgba(0, 0, 0, 0) url("imges/head-arrow.png") no-repeat scroll 0 0;
    content: "";
    height: 98px;
    position: absolute;
    right: -30px;
    top: -6px;
    width: 69px;
}
.premium-box-container .pho-desc-head {
    margin: 0 auto;
    position: relative;
    width: 768px;
}
.premium-box-container .description {
    text-align: center;
}

.pho-plugin-content p {
    color: #212121;
    font-size: 18px;
    line-height: 32px;
}

.premium-box-container .pho-desc-head h2 {
    color: #02c277;
    font-size: 28px;
    font-weight: bolder;
    margin: 0;
    text-transform: capitalize;
    line-height: 30px;
}

.premium-box-container .description:nth-child(2n+1) .pho-img-bg {
    background: #f1f1f1 url("<?php echo $plugin_dir_url; ?>images/image-frame-odd.png") no-repeat scroll 100% top;
}
.description .pho-plugin-content .pho-img-bg {
    border-radius: 5px 5px 0 0;
    height: auto;
    margin: 0 auto;
    padding: 70px 0 40px;
    width: 750px;
}

.premium-box-container .description:nth-child(2n) .pho-img-bg {
    background: #f1f1f1 url("<?php echo $plugin_dir_url; ?>images/image-frame-even.png") no-repeat scroll 100% top;
}

.premium-box-container .description:nth-child(2n) {
    background: #eae8e7 none repeat scroll 0 0;
}
 
 
.premium-box-container .pho-desc-head::after {
    background: rgba(0, 0, 0, 0) url("<?php echo $plugin_dir_url; ?>images/head-arrow.png") no-repeat scroll 0 0;
    content: "";
    height: 98px;
    position: absolute;
    right: -30px;
    top: 0;
    width: 69px;
} 

.pho-plugin-content {
    margin: 0 auto;
    overflow: hidden;
    width: 768px;
} 

.pho-upgrade-btn {
    display: block;
    text-align: center;
}


.pho-upgrade-btn a {
    display: inline-block;
    margin-top: 75px;
}
                     					 
				</style>
		<div class="premium-box">	
            <div class="premium-box-head">
        <div class="pho-upgrade-btn">
					
						
						<a href="http://www.phoeniixx.com/product/woocommerce-login-signup" target="_blank"><img src="<?php echo $plugin_dir_url; ?>images/premium-btn.png" /></a>
						<a target="blank" href="http://loginsignup.phoeniixxdemo.com/"><img src="<?php echo $plugin_dir_url; ?>images/demo-btns.png" /></a>

						</div>
					</div>
											
						<ul class="premium-features">
							<div class="main-heading"><h1><img src="<?php echo $plugin_dir_url; ?>images/premium-head.png" /></h1></div>
                            
       <div class="premium-box-container">
        <div class="description">
            <div class="pho-desc-head"><h2>Set Popup Layout</h2></div>
            <div class="pho-plugin-content">
                        <p>
								  The plugin lets you set the Popup Layout whenever you require to do so. You are provided with three Layout choices, out of which you could select one.
								</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option1.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        
        <div class="description">
            <div class="pho-desc-head"><h2>Set Popup Style</h2></div>
            <div class="pho-plugin-content">
                        <p>
								 The plugin gives you two choices of Popup Style. You could either set the Popup Style as Lightbox or as Dropdown.
								</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option2.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        
        <div class="description">
            <div class="pho-desc-head"><h2>Add Auto Load</h2></div>
            <div class="pho-plugin-content">
                        <p>
								 You could let the popup load automatically and could set the desired number of seconds  after which you require the popup to appear on the loaded page.
								</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option3.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        
        <div class="description">
            <div class="pho-desc-head"><h2>Enable/Disable Terms &amp; Conditions</h2></div>
            <div class="pho-plugin-content">
                        <p>
									 You could Enable or Disable T&amp;C on Registration Page. You could also change the label color. 
									</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option4.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
         <div class="description">
            <div class="pho-desc-head"><h2>Integrate Social Login into Popup</h2></div>
            <div class="pho-plugin-content">
                        <p>
									 With this feature, you could enable social login through Popup/Pages and set the Social Account Layout either as ‘Up’ or as ‘down’. You could upload FB and Google+ icon images as well. 
									</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option5.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        
        <div class="description">
            <div class="pho-desc-head"><h2>Set Social Account Layout</h2></div>
            <div class="pho-plugin-content">
                        <p>
									This feature allows you to set the position of Social icons. As per your preference,  you could either keep it as 'Up' or as 'Down'. 
									</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option6.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
         <div class="description">
            <div class="pho-desc-head"><h2>Customize Labels in Login Form, Register Form & Pop Open Link</h2></div>
            <div class="pho-plugin-content">
                        <p>
									 You could write your own ‘Heading’, ‘Username’ Label, ‘Password’ Label, ‘Lost Password’ Label, ‘Remember Me’ Label &amp; ‘Login Button’ Label for Login Form. And for Register Form, you could write your own ‘Heading’ Label, ‘Password Label’, ‘Email’ Label &amp; ‘Register Button’ Label.For Pop open link, you could write ‘Login’ Label and ‘Register’ Label.
									</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option7.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        
        <div class="description">
            <div class="pho-desc-head"><h2>Stylize by Using Advanced Customization Options</h2></div>
            <div class="pho-plugin-content">
                        <p>
									Advanced customization options for stylization allow you to select BG Color, BG Image, Border Color, Size &amp; Style, Popup &amp; Button’s Color, Font Size, Border etc. 
									</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option8.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        <div class="description">
            <div class="pho-desc-head"><h2>More of Advanced Customization Options</h2></div>
            <div class="pho-plugin-content">
                        <p>
									You could set field placeholder for ‘Username’, ‘Email’ and ‘Password’, as per your choice. 
									The advanced settings also give you the option to set your own link label for ‘Forget Password’, ‘Login’ &amp; ‘Register’. 

									</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option9.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        <div class="description">
            <div class="pho-desc-head"><h2>Description Box for Login &amp; Register</h2></div>
            <div class="pho-plugin-content">
                        <p>
									 The plugin gives you the option to have a Login Popup Description Box and a Register Popup Description Box. You could add relevant descriptions to these boxes. You could also set Login Popup Title Text, Login Button Label, Register Popup Title text and Register Button Label.
									</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option10.png" />
                        </div>
                    </div>
       
        </div><!-- description end -->
        
        
             
						<div class="description">	
						
								 <div class="pho-desc-head"><h2>Select Popup ‘Entrance’ Effects</h2>
								</div>
                                <div class="pho-plugin-content">
                                	<p>
									 You could select a Popup Entrance Effect from the given list of Effects. The list includes bounceIn, fadIn, slideInUp, rotateIn and many more similar options.
									</p>
                                    
                                    <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option11.png" />
                        </div>
                                    
                                   </div>
       
        </div><!-- description end -->
        
        
        <div class="description">	
						
								 <div class="pho-desc-head"><h2>Select Popup 'Exit' Effects</h2></div>
                                <div class="pho-plugin-content">
                                	<p>
									You could select Popup Exit Effect from the list of Effects provided to you. Some of the Effects that are available for you to choose include- bounceOut, fadeOut, rotateOut &amp; slideOutUp.
									</p>
                                    
                                    <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option12.png" />
                        </div>
                                    
                                   </div>
       
        </div><!-- description end -->
        
        
        <div class="description">	
						
								 <div class="pho-desc-head"><h2>Mobile Compatibility</h2></div>
                                <div class="pho-plugin-content">
                                	<p>
									 The Popup feature is mobile compatible as well.
									</p>
                                    
                                    <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>images/prm_option13.png" />
                        </div>
                                    
                                   </div>
       
        </div><!-- description end -->
        
        <div class="description">	
						
								 <div class="pho-desc-head"><h2>Direct Access to 'My Account' Page</h2></div>
                                <div class="pho-plugin-content">
                                	<p>
									This feature allows a new or existing user to directly access 'My Account' Page, as soon as he/she Signs Up or Logs Into his/her account on your website.
									</p>
                                 <div class="pho-img-bg">
                                    
                      <span>  <img src="<?php echo $plugin_dir_url; ?>images/prm_option14.png" /></span>  
                      <span>  <img src="<?php echo $plugin_dir_url; ?>images/prm_option14-front.png" /></span>  
                        </div>
                                    
                                   </div>
       
        </div><!-- description end -->
        
        <div class="description">	
						
								 <div class="pho-desc-head"><h2>Add 'Google Recaptcha' at the time of Signup</h2></div>
                                <div class="pho-plugin-content">
                                	<p>
									 This option allows you to place 'Google Recaptcha' feature before a new User Signs Up. The feature lets you ensure that the new user is a human and not a robot.
									</p>
                                    
                                    <div class="pho-img-bg">
                        <span>  <img src="<?php echo $plugin_dir_url; ?>images/prm_option15.png" /></span>  
                      <span>  <img src="<?php echo $plugin_dir_url; ?>images/prm_option15-front.png" /></span>  
                        </div>
                                    
                                   </div>
       
        </div><!-- description end -->
        
        
        <div class="pho-upgrade-btn">
        	<a href="http://www.phoeniixx.com/product/woocommerce-login-signup" target="_blank"><img src="<?php echo $plugin_dir_url; ?>images/premium-btn.png" /></a>
			<a target="blank" href="http://loginsignup.phoeniixxdemo.com/"><img src="<?php echo $plugin_dir_url; ?>images/demo-btns.png" /></a>
        </div>
        
        
        
       </div><!-- premium-box-container -->
							
						 
						</ul>
						
				   </div>
	
<?php
	}
	
		wp_enqueue_script("conditions-js",plugins_url( '' , __FILE__ ).'/assets/js/admin.js',array('jquery'),'',true);
    
	}
	
}
else
{ 

?>

    <div class="error notice is-dismissible " id="message"><p>Please <strong>Activate</strong> WooCommerce Plugin First, to use woocommerce Social Login.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
        
<?php 

}
ob_clean();
?>
