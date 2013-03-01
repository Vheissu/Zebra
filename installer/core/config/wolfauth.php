<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// The config for Wolfauth
$config['wolfauth'] = array(

	// Password hashing method. Unless you know what you are doing
	// It is not advisable you change this
	'hash.method' => 'sha256',

	// Hash key by default uses the encryption key in the config/config.php file
	// You can change this to something else if you wish
	'hash.key' => config_item('encryption_key'),

	//The database field used to log a user in
	'login.field' => 'username',
	
	// The name of the site (used for emails, etc)
	'site.name' => 'Wolfauth Test',
	
	// Which email address should all auth emails come from
	'site.admin_email' => 'do-not-reply@localhost',

	// Are registered users required to confirm their account by email?
	'register.require_email_confirmation' => 1,
	
	// Register status (1 is enabled and 0 is disabled)
	'register.status' => 1,

	// The amount of failed login attempts before you're banned for a specified amount of time
	'login.max_attempts' => 3,

	// How long to lock out someone after 3 failed attempts in seconds
	// Default is 10 minutes which equals 600 seconds
	'login.max_attempts_lockout' => 600,

	// What roles are considered admin
	'roles.admin' => array('admin', 'super_admin')
	
);