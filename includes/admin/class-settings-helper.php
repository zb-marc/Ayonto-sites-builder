<?php
/**
 * Settings Helper
 *
 * Provides easy access to plugin settings throughout the codebase.
 *
 * @package    Ayonto_Sites
 * @subpackage Admin
 * @since      0.1.28
 */

namespace Ayonto\Sites\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings_Helper class
 *
 * Static helper methods for accessing settings.
 *
 * @since 0.1.28
 */
class Settings_Helper {

	/**
	 * Get company name
	 *
	 * @return string Company name.
	 */
	public static function get_company_name() {
		return Settings::get( 'company_name', 'Ayonto' );
	}

	/**
	 * Get company URL
	 *
	 * @return string Company URL.
	 */
	public static function get_company_url() {
		return Settings::get( 'company_url', home_url( '/' ) );
	}

	/**
	 * Get default brand
	 *
	 * @return string Default brand.
	 */
	public static function get_default_brand() {
		return Settings::get( 'default_brand', 'Ayonto' );
	}

	/**
	 * Get company logo URL
	 *
	 * @return string Company logo URL.
	 */
	public static function get_company_logo() {
		$logo = Settings::get( 'company_logo', '' );
		
		// Fallback to site icon if no logo is set.
		if ( empty( $logo ) ) {
			$logo = get_site_icon_url();
		}
		
		return $logo;
	}

	/**
	 * Get schema organization name
	 *
	 * @return string Organization name for Schema.org.
	 */
	public static function get_schema_org_name() {
		$name = Settings::get( 'schema_org_name', '' );
		
		// Fallback to company name if not set.
		if ( empty( $name ) ) {
			$name = self::get_company_name();
		}
		
		return $name;
	}

	/**
	 * Get schema organization URL
	 *
	 * @return string Organization URL for Schema.org.
	 */
	public static function get_schema_org_url() {
		$url = Settings::get( 'schema_org_url', '' );
		
		// Fallback to company URL if not set.
		if ( empty( $url ) ) {
			$url = self::get_company_url();
		}
		
		return $url;
	}

	/**
	 * Get schema organization description
	 *
	 * @return string Organization description for Schema.org.
	 */
	public static function get_schema_org_description() {
		return Settings::get( 'schema_org_description', '' );
	}

	/**
	 * Get schema contact point
	 *
	 * @return array|null Contact point data or null if not configured.
	 */
	public static function get_schema_contact_point() {
		$type      = Settings::get( 'schema_contact_type', '' );
		$telephone = Settings::get( 'schema_contact_telephone', '' );
		$email     = Settings::get( 'schema_contact_email', '' );

		// Return null if no contact info is set.
		if ( empty( $type ) || ( empty( $telephone ) && empty( $email ) ) ) {
			return null;
		}

		$contact = array(
			'@type'       => 'ContactPoint',
			'contactType' => $type,
		);

		if ( ! empty( $telephone ) ) {
			$contact['telephone'] = $telephone;
		}

		if ( ! empty( $email ) ) {
			$contact['email'] = $email;
		}

		return $contact;
	}

	/**
	 * Get primary color
	 *
	 * @return string Primary color hex code.
	 */
	public static function get_primary_color() {
		return Settings::get( 'primary_color', '#004B61' );
	}

	/**
	 * Get secondary color
	 *
	 * @return string Secondary color hex code.
	 */
	public static function get_secondary_color() {
		return Settings::get( 'secondary_color', '#F0F4F5' );
	}

	/**
	 * Get accent color
	 *
	 * @return string Accent color hex code.
	 */
	public static function get_accent_color() {
		return Settings::get( 'accent_color', '#F79D00' );
	}

	/**
	 * Get border color
	 *
	 * @return string Border color hex code.
	 */
	public static function get_border_color() {
		return Settings::get( 'border_color', '#e5e7eb' );
	}

	/**
	 * Get import auto-brand setting
	 *
	 * @return bool Whether to auto-assign brand.
	 */
	public static function get_import_auto_brand() {
		return (bool) Settings::get( 'import_auto_brand', true );
	}

	/**
	 * Get import batch size
	 *
	 * @return int Batch size for imports.
	 */
	public static function get_import_batch_size() {
		return absint( Settings::get( 'import_batch_size', 200 ) );
	}

	/**
	 * Get import max file size
	 *
	 * @return int Max file size in MB.
	 */
	public static function get_import_max_file_size() {
		return absint( Settings::get( 'import_max_file_size', 10 ) );
	}

	/**
	 * Get auto-inject specs setting
	 *
	 * @return bool Whether to auto-inject specs.
	 */
	public static function get_auto_inject_specs() {
		return (bool) Settings::get( 'auto_inject_specs', false );
	}

	/**
	 * Get spec table style
	 *
	 * @return string Spec table style.
	 */
	public static function get_spec_table_style() {
		return Settings::get( 'spec_table_style', 'default' );
	}

	/**
	 * Get show icons setting
	 *
	 * @return bool Whether to show icons in tables.
	 */
	public static function get_show_icons() {
		return (bool) Settings::get( 'show_icons', false );
	}

	/**
	 * Get all colors as CSS variables
	 *
	 * @return string CSS variables for colors.
	 */
	public static function get_css_variables() {
		return sprintf(
			':root { --vt-primary: %s; --vt-secondary: %s; --vt-accent: %s; --vt-border: %s; }',
			esc_attr( self::get_primary_color() ),
			esc_attr( self::get_secondary_color() ),
			esc_attr( self::get_accent_color() ),
			esc_attr( self::get_border_color() )
		);
	}
}
