<?php
/*
Plugin Name: WP ImageHost
Plugin URI: http://www.milchrausch.de/wordpress-plugin-wp-imagehost/
Description: The Wordpress Plugin WP ImageHost is a simple to use and easy to install plugin to host your images inside your blogposts on a separate subdomain to reduce the number of requests and maximize your weblogs speed.
Version: 1.0
Author: Hauke Leweling
Author URI: http://www.milchrausch.de/
Update Server: http://www.milchrausch.de/
Min WP Version: 2.9
Max WP Version: 3.0
*/


add_action("admin_menu", array('WP_ImageHost', 'add_menu'));

global $wp_rewrite;
class WP_ImageHost
{
	const ERROR_UPDATE_URL_PATH = "There was an error while updating your imagepath";
	const INSTALL_SUCCESS = "Installation successfull!";
	
	function __construct()
	{

	}
	
	function option_page()
	{
		global $wpdb;
		
		$Success = false;
		$AdminPage = null;
		
		$TableName = $wpdb->prefix . "posts";
		$Subdomain = $_POST['wp_imagehost_subdomain'];
		
		if(isset($_POST['update_upload_url_path']) AND !empty($_POST['wp_imagehost_subdomain']))
		{
			if(substr($Subdomain, strlen($Subdomain)-1, strlen($Subdomain)) == "/")
			{
				$Subdomain = substr($Subdomain, 0, strlen($Subdomain)-1);
			}
			
			update_option("upload_url_path", $Subdomain);
			
				if($_POST['wp_imagehost_convert_old'] == "true")
				{
					$Query = 'UPDATE `'.$TableName.'`SET `post_content` = REPLACE(`post_content`,"'.get_option("siteurl").'/wp-content/uploads/","'.$_POST['wp_imagehost_subdomain'].'/")';

					$wpdb->query($Query); 
					
					$Query = 'UPDATE `'.$TableName.'` SET `guid` = REPLACE(`guid`,"'.get_option("siteurl").'/wp-content/uploads/","'.$_POST['wp_imagehost_subdomain'].'/")';

					$wpdb->query($Query);
				}
			$Success = true;
		}
		
		
		$AdminPage .= "
					<div class=\"wrap\">
						<h2>Wordpress Image Host Service</h2>";
						
						if($Success == true) $AdminPage .= "<h3 style='color:#FF0000;'>".self::INSTALL_SUCCESS."</h3>";
		
						$AdminPage .= "<table>
						<tr>
						<td width='60%'>
						<h3>About WP ImageHost</h3>
						By default, the most Web browser will create from two to four connections to the Web server, for downloading a Web page. This will results in slow Web page displaying, especially if the requested Web page contains more than one image. However, we can actually trick the Web browser, by hosting the requested images in other domains. The imageurl has not to be a real domain, we can host images on a subdomain!The Web browser will download the images in parallel with the rest of the Web pages.<br />
						<br />
						The usage of this Wordpress plugin requires just a subdomain whicht points to the Wordpress upload folder. Normaly this is located in <i>\"/wp-content/upload\"</i><br />
						<br />
						
						<h3>Configuration</h3>
						<form name=\"wp_imagehost_config\" action=\"".get_settings("siteurl")."/wp-admin/options-general.php?page=wp-imagehost/index.php\" method=\"post\">
						
						We just need 5 little steps to configure <strong>WP ImageHost</strong><br />
						<b>Step 1</b> Go to your Webhosting controlpanel and create a subdomain which points to your Wordpress upload folder which is normally located in <i>\"/wp-content/upload\"</i> (<a href='#' onclick=\"document.getElementById('example').style.display = 'block'\";>Example</a>)<br />
						<p id='example' style='display:none;'><img src=\"".plugins_url('/images/create_subdomain.png', __FILE__)."\" alt=\"create subdomain\" /><br /></p>
						<b>Step 2</b> Enter your subdomain to the form below<br />
						Subdomain: <input type=\"text\" name=\"wp_imagehost_subdomain\" value=\"".get_option("upload_url_path")."\" style=\"font-weight:bold;font-size:15pt;width:350px;padding:3px;\"/><br />
						<b>Step 3</b> If you want to convert all your existing posts check thr form below<br />
						convert old posts:<input type=\"checkbox\" name=\"wp_imagehost_convert_old\" value=\"true\"/><br />
						<b>Step 4</b> To tell google and your visitors the new location of your images, just add the following line to your .htaccess file <br />
						<code>RedirectMatch 301 ^/wp-content/uploads/(.*)$    http://subdomain.yourdomain.tld/$1</code><br />
						<b>Step 5</b> Just submit your settings!<br />
						<input type=\"submit\" class=\"button-primary\" name=\"update_upload_url_path\" value=\"submit\" style=\"font-weight:bold;font-size:15pt;width:350px;padding:3px;\"/>

						</form>
						</td>
						<td valign=\"top\">
							<h3>Questions</h3>
							If there are questions or bugreports feel free to submit them on my website<br />
							<a href=\"http://www.milchrausch.de/wordpress-plugin-wp-imagehost/\" target=\"_blank\">Authors Website</a>
							<h3>Donate</h3>
							If you like this plugin feel free to make a donation or link my <a href=\"http://www.milchrausch.de/\" target=\"_blank\">Blog</a><br />
							<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
								<input type='hidden' name='cmd' value='_s-xclick'>
								<input type='hidden' name='hosted_button_id' value='8LXKXCT3NU7E6'>
								<input type='image' src='https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif' border='0' name='submit'>
								<img alt='' border='0' src='https://www.paypal.com/de_DE/i/scr/pixel.gif' width='1' height='1'>
							</form>

						
						</td>
						</table>
					</div>	
					";

		echo $AdminPage;
	}
	
	function convert_database()
	{
	
	}
	
	function add_menu()
	{
		add_options_page('WP_ImageHost', 'WP ImageHost', 9, __FILE__, array('WP_ImageHost','option_page'));
	}
}
?>