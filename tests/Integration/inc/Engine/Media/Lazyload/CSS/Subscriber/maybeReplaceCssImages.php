<?php

namespace WP_Rocket\Tests\Integration\inc\Engine\Media\Lazyload\CSS\Subscriber;

use WP_Rocket\Tests\Integration\FilesystemTestCase;
use WP_Rocket\Tests\Integration\FilterTrait;
use Brain\Monkey\Functions;

/**
 * @covers \WP_Rocket\Engine\Media\Lazyload\CSS\Subscriber::maybe_replace_css_images
 */
class Test_maybeReplaceCssImages extends FilesystemTestCase {

	use FilterTrait;

	protected $path_to_test_data = '/inc/Engine/Media/Lazyload/CSS/Subscriber/integration/maybeReplaceCssImages.php';

	protected $config;

	public function set_up()
	{
		parent::set_up();
		$this->unregisterAllCallbacksExcept('rocket_buffer', 'maybe_replace_css_images', 1002);

		add_filter('pre_get_rocket_option_lazyload_css_bg_img', [$this, 'lazyload_css_bg_img']);
	}

	public function tear_down()
	{
		remove_filter('pre_get_rocket_option_lazyload_css_bg_img', [$this, 'lazyload_css_bg_img']);
		$this->restoreWpFilter('rocket_buffer');
		parent::tear_down();
	}

	/**
     * @dataProvider providerTestData
     */
    public function testShouldReturnAsExpected( $config, $expected )
    {
		$this->config = $config;
		Functions\when('rocket_get_constant')->alias(function ($name, $default = null) {
			if('ABSPATH' === $name) {
				return $this->filesystem->getUrl('/');
			}

			if('WP_CONTENT_DIR' === $name) {
				return $this->filesystem->getUrl('/') . 'wp-content';
			}

			if(defined($name)) {
				return constant($name);
			}

			return $default;
		});

		Functions\when('wp_generate_uuid4')->alias(function () {
			return 'hash';
		});

        $this->assertSame($expected['output'], apply_filters('rocket_buffer', $config['html']));
    	foreach($expected['files'] as $path => $content) {

			$this->assertSame($content['exists'], $this->filesystem->exists($path));
			$this->filesystem->chmod($path, 0777);
			if(! $content['exists']) {
				continue;
			}

			$expected_content = trim($content['content']);
			$content = trim($this->filesystem->get_contents($path));

			$this->assertSame($expected_content, $content);

		}
	}

	public function lazyload_css_bg_img() {
		return $this->config['lazyload_css_bg_img'];
	}
}
