<div class="wrap extendons-support">
		
			<h3><?php _e('Welcome to Extendons Support – We are here to help', 'eopa') ?></h3>

			<div class="about-text"><?php _e('Our customer support team is powered with enthusiasm to serve you the best in solving a technical issue or answering your queries in time. If you have got a question, please do not hesitate to ask us in this easy to fill form, and we assure you a prompt reply.', 'eopa') ?>
				
			</div>
			
			<?php 
		
			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
			}


			$a = 0;
			foreach ( $active_plugins as $plugin ) {

					$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
					
					if($plugin_data['AuthorName'] == 'Extendons') {
						$a++;
					}
					
			}

			?>

			<div class="extendons-logo">
				<img src="<?php echo EOPA_URL.'images/logo-extendon.png'; ?>" >
				<span class="extendons-faq-version"></span>
			</div>

			<div class="extendons-support-active">

					<table class="widefat" cellspacing="0" id="status">

						<thead>
							<tr>
								<th><?php _e('Extendon Active Plugin', 'eopa'); ?> (<?php echo $a; ?>)</th>
								<th><?php _e('Version', 'eopa'); ?></th>
								<th><?php _e('Company', 'eopa'); ?></th>
							</tr>
						</thead>
						
						<tbody>
							<?php
							foreach ( $active_plugins as $plugin ) {

							$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
							$dirname        = dirname( $plugin );
							$version_string = '';
							$network_string = '';

							if ( in_array('Extendons', $plugin_data)) {

								// Link the plugin name to the plugin url if available.
								if ( ! empty( $plugin_data['PluginURI'] ) ) {
									$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'eopa' ) . '">' . esc_html( $plugin_data['Name'] ) . '</a>';
								} else {
									$plugin_name = esc_html( $plugin_data['Name'] );
								}
								?>
								<tr>
									<td>
										<?php echo $plugin_name; ?>
									</td>
									<td>
										<?php echo $plugin_data['Version']; ?>
									</td>
									<td>
										<?php printf( esc_attr__( 'by %s', 'eopa' ), '<a href="' . esc_url( $plugin_data['AuthorURI'] ) . '" target="_blank">' . esc_html( $plugin_data['AuthorName'] ) . '</a>' ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?>
									</td>
								</tr>
								<?php
							}
							} ?>
						</tbody>

					</table>

			</div>


			<div class="extendons-support-form">

				<h4><?php _e('Contact Extendon Support Team', 'eopa') ?></h4>
				
				<h5 id="extendon_sup_success"><?php _e('Your message has been successfully sent. We will contact you very soon!', 'eopa') ?></h5>
				
				<form id="extendon-form-support">
					
					<div class="extendon-field">
						<!-- <label>Enter First Name</label> -->
						<input data-parsley-required type="text" id="ex_customer_fname" name="ex_customer_fname" placeholder="<?php _e('First Name', 'eopa'); ?>">
						<!-- <label>Enter Last Name</label> -->
						<input data-parsley-required type="text" id="ex_customer_lname" name="ex_customer_lname" placeholder="<?php _e('Last Name', 'eopa') ?>">
					</div>

					<div class="extendon-field">
						<!-- <label>Enter Your Email</label> -->
						<input data-parsley-required type="email" id="ex_customer_email" name="ex_customer_email" placeholder="<?php _e('Your Email', 'eopa') ?>">
						<!-- <label>Enter Your Phone</label> -->
						<input type="number" min="0" id="ex_customer_number" name="ex_customer_number" placeholder="Phone Number">
					</div>

					<div class="extendon-field">
						<!-- <label>Subject</label> -->
						<input type="text" id="ex_customer_subject" name="ex_customer_subject" placeholder="Subject">
						<!-- <label>Select Module</label> -->
						<select id="ex_support_module" name="ex_support_module">
							<option value="0" ><?php _e('Select Plugin', 'eopa') ?></option>
							<option selected="selected" value="extendon-faq"><?php echo $plugin_name; ?></option>
						</select>
					</div>

					<div class="extendon-field">
						<!-- <label>Your Message</label> -->
						<textarea data-parsley-required rows="8" id="ex_customer_message" name="ex_customer_message" placeholder="<?php _e('Message', 'eopa') ?>"></textarea>
					</div>

					<div class="extendon-field">
						<!-- <label></label> -->
						<input id="extendon-submit" type="button" onclick="paextendsupport()" name="" value="<?php _e('Send Request', 'eopa') ?>">
					</div>
				
				</form>

				<div class="extendon-socials">
					<ul class="extend-social-left">
						<li>
							<a target="_blank" href="https://www.facebook.com/extendons/">
								<img src="<?php echo EOPA_URL.'images/fb.png'; ?>">
							</a>
						</li>
						<li>
							<a target="_blank" href="https://plus.google.com/u/8/114047538741272702397">
								<img src="<?php echo EOPA_URL.'images/google_plus.png'; ?>">
							</a>
						</li>
						<li>
							<a target="_blank" href="http://extendons.com/">
								<img src="<?php echo EOPA_URL.'images/avatar-80x80.png'; ?>" >
							</a>
						</li>
						<li>
							<a target="_blank" href="https://www.linkedin.com/company/extendons">
								<img src="<?php echo EOPA_URL.'images/linkedin.png'; ?>">
							</a>
						</li>
						<li>
							<a target="_blank" href="https://twitter.com/extendons">
								<img src="<?php echo EOPA_URL.'images/twitter.png'; ?>">
							</a>
						</li>
					</ul>
				</div>

			</div>

		</div>
	
		<script type="text/javascript">
			
			// support email
			function paextendsupport() { 
				
				jQuery('#extendon-form-support').parsley().validate();	
				var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var condition = 'paextendons_support_contact';
				var suppextfname = jQuery('#ex_customer_fname').val();
				var suppextlname = jQuery('#ex_customer_lname').val();
				var suppextemail = jQuery('#ex_customer_email').val();
				var suppextnumber = jQuery('#ex_customer_number').val();
				var suppextsubj = jQuery('#ex_customer_subject').val();
				var suppextmasg = jQuery('#ex_customer_message').val();
				if(suppextfname == '' && suppextlname == '' && suppextemail == '' && suppextmasg == '') {
					return false;
				}else if (suppextfname == '') { 
					return false;
				} else if(suppextlname == '') {
					return false;
				} else if (suppextemail == '') {
					return false;
				}else if (!pattern.test(suppextemail)) {
					return false;
				}else if (suppextmasg == '' ) {
					return false;
				}else {

					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'pasupport_extend_contact',
							condition : condition,
							suppextfname : suppextfname,
							suppextlname : suppextlname,
							suppextemail : suppextemail,
							suppextnumber : suppextnumber,
							suppextsubj : suppextsubj,
							suppextmasg : suppextmasg,		

						},
						success : function(response) {
							jQuery('#extendon_sup_success').show().delay(3000).fadeOut();
							jQuery('#extendon-form-support').each(function() {
								this.reset(); 
							});
						}
					});
				}
			}

		</script>
