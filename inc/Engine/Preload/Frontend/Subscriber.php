<?php

namespace WP_Rocket\Engine\Preload\Frontend;

use WP_Rocket\Engine\Preload\Controller\CheckFinished;
use WP_Rocket\Engine\Preload\Controller\PreloadUrl;
use WP_Rocket\Event_Management\Subscriber_Interface;

class Subscriber implements Subscriber_Interface {

	/**
	 * Controller fetching the sitemap.
	 *
	 * @var FetchSitemap
	 */
	protected $fetch_sitemap;

	/**
	 * Controller preloading urls.
	 *
	 * @var PreloadUrl
	 */
	protected $preload_controller;

	/**
	 * Controller checking if the preload is finished.
	 *
	 * @var CheckFinished
	 */
	protected $check_finished;

	/**
	 * Creates an instance of the class.
	 *
	 * @param FetchSitemap  $fetch_sitemap controller fetching the sitemap.
	 * @param PreloadUrl    $preload_controller controller preloading urls.
	 * @param CheckFinished $check_finished controller checking if the preload is finished.
	 */
	public function __construct( FetchSitemap $fetch_sitemap, PreloadUrl $preload_controller, CheckFinished $check_finished ) {
		$this->fetch_sitemap      = $fetch_sitemap;
		$this->preload_controller = $preload_controller;
		$this->check_finished     = $check_finished;
	}

	/**
	 * Return an array of events that this subscriber listens to.
	 *
	 * @return array
	 */
	public static function get_subscribed_events() {
		return [
			'rocket_preload_job_parse_sitemap'  => 'parse_sitemap',
			'rocket_preload_job_preload_url'    => 'preload_url',
			'rocket_preload_job_check_finished' => 'check_finished',
		];
	}

	/**
	 * Parse the sitemap.
	 *
	 * @param string $url url to parse.
	 * @return void
	 */
	public function parse_sitemap( string $url ) {
		$this->fetch_sitemap->parse_sitemap( $url );
	}

	/**
	 * Preload url.
	 *
	 * @param string $url url to preload.
	 * @return void
	 */
	public function preload_url( string $url ) {
		$this->preload_controller->preload_url( $url );
	}

	/**
	 * Check if the preload is finished.
	 *
	 * @return void
	 */
	public function check_finished() {
		$this->check_finished->check_finished();
	}
}
