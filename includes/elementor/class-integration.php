<?php
namespace Ayonto\Sites\Elementor;

class Integration {
	private static $instance = null;
	
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_action('elementor/query/vt_battery', array($this, 'custom_query'));
		
		// Build 048: Initialize Dynamic Tags.
		Dynamic_Tags::get_instance();
	}
	
	public function custom_query($query) {
		// Custom query modifications will be added here
	}
}
