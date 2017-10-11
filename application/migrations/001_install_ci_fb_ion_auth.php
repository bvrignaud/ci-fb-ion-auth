<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Install_ci_fb_ion_auth extends CI_Migration
{
	private $tables;

	public function __construct() {
		parent::__construct();
		$this->load->dbforge();

		$this->load->config('ion_auth', TRUE);
		$this->tables = $this->config->item('tables', 'ion_auth');
	}

	public function up() {
	    // Drop table 'facebook_user' if it exists
		$this->dbforge->drop_table('facebook_user', TRUE);

		// Table structure for table 'users'
		$this->dbforge->add_field(array(
			'idfacebook_user' => array(
				'type'           => 'BIGINT',
				'constraint'     => '20',
			),
			'user_id' => array(
				'type'       => 'INT',
				'constraint' => '11',
				'unsigned'   => TRUE
			),
		));
		$this->dbforge->add_key('idfacebook_user', TRUE);
		$this->dbforge->create_table('facebook_user');
		
		// Add avatar column to ion_auth.users
		$this->dbforge->add_column($this->tables['users'], [
		    'avatar' => [
		        'type' => 'VARCHAR',
		        'constraint' => '45',
		    ],
		]);
	}

	public function down() {
	    $this->dbforge->drop_column($this->tables['users'], 'avatar');
		$this->dbforge->drop_table('facebook_user', TRUE);
	}
}
