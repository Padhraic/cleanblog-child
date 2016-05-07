<?php
namespace TenUp\cleanblogchild\Core;

/**
 * This is a very basic test case to get things started. You should probably rename this and make
 * it work for your project. You can use all the tools provided by WP Mock and Mockery to create
 * your tests. Coverage is calculated against your includes/ folder, so try to keep all of your
 * functional code self contained in there.
 *
 * References:
 *   - http://phpunit.de/manual/current/en/index.html
 *   - https://github.com/padraic/mockery
 *   - https://github.com/10up/wp_mock
 */

use TenUp\cleanblogchild as Base;

class Core_Tests extends Base\TestCase {

	protected $testFiles = [
		'functions/core.php'
	];

	/**
	 * Make sure all theme-specific constants are defined before we get started
	 */
	public function setUp() {
		if ( ! defined( 'WPTHEME_TEMPLATE_URL' ) ) {
			define( 'WPTHEME_TEMPLATE_URL', 'template_url' );
		}
		if ( ! defined( 'WPTHEME_VERSION' ) ) {
			define( 'WPTHEME_VERSION', '0.1.0' );
		}
		if ( ! defined( 'WPTHEME_URL' ) ) {
			define( 'WPTHEME_URL', 'url' );
		}

		parent::setUp();
	}

	/**
	 * Test setup method.
	 */
	public function test_setup() {
		// Setup
		\WP_Mock::expectActionAdded( 'after_setup_theme',  'TenUp\cleanblogchild\Core\i18n'        );
		\WP_Mock::expectActionAdded( 'wp_enqueue_scripts', 'TenUp\cleanblogchild\Core\scripts'     );
		\WP_Mock::expectActionAdded( 'wp_enqueue_scripts', 'TenUp\cleanblogchild\Core\styles'      );
		\WP_Mock::expectActionAdded( 'wp_head',            'TenUp\cleanblogchild\Core\header_meta' );

		// Act
		setup();

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * Test internationalization integration.
	 */
	public function test_i18n() {
		// Setup
		\WP_Mock::wpFunction( 'load_theme_textdomain', array(
			'times' => 1,
			'args' => array(
				'wptheme',
				WPTHEME_PATH . '/languages'
			),
		) );

		// Act
		i18n();

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * Test scripts enqueue.
	 */
	public function test_scripts() {
		// Regular
		\WP_Mock::wpFunction( 'wp_enqueue_script', array(
			'times' => 1,
			'args' => array(
				'wptheme',
				'template_url/assets/js/cleanblog-child.min.js',
				array(),
				'0.1.0',
				true,
			),
		) );

		scripts();
		$this->assertConditionsMet();

		// Debug Mode
		\WP_Mock::wpFunction( 'wp_enqueue_script', array(
			'times' => 1,
			'args' => array(
				'wptheme',
				'template_url/assets/js/cleanblog-child.js',
				array(),
				'0.1.0',
				true,
			),
		) );
		\WP_Mock::onFilter( 'special_filter' )
		        ->with( 'wptheme_script_debug' )
		        ->reply( true );

		scripts();
		$this->assertConditionsMet();
	}

	/**
	 * Test style enqueue.
	 */
	public function test_styles() {
		// Regular
		\WP_Mock::wpFunction( 'wp_enqueue_style', array(
			'times' => 1,
			'args' => array(
				'wptheme',
				'url/assets/css/cleanblog-child.min.css',
				array(),
				'0.1.0',
			),
		) );

		styles();
		$this->assertConditionsMet();

		// Debug Mode
		\WP_Mock::wpFunction( 'wp_enqueue_style', array(
			'times' => 1,
			'args' => array(
				'wptheme',
				'url/assets/css/cleanblog-child.css',
				array(),
				'0.1.0',
			),
		) );
		\WP_Mock::onFilter( 'special_filter' )
		        ->with( 'wptheme_style_debug' )
		        ->reply( true );

		styles();
		$this->assertConditionsMet();
	}

	/**
	 * Test header meta injection
	 */
	public function test_header_meta() {
		// Setup
		$url = 'template_url/humans.txt';
		$meta = '<link type="text/plain" rel="author" href="template_url/humans.txt" />';
		\WP_Mock::onFilter( 'wptheme_humans' )->with( $url )->reply( $url );
		\WP_Mock::wpPassThruFunction( 'esc_url' );

		// Act
		ob_start();
		header_meta();
		$result = ob_get_clean();

		// Verify
		$this->assertConditionsMet();
		$this->assertEquals( $meta, $result );
	}
}
