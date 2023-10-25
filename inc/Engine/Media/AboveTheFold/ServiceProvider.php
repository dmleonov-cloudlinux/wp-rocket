<?php
declare(strict_types=1);

namespace WP_Rocket\Engine\Media\AboveTheFold;

use WP_Rocket\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use WP_Rocket\Engine\Media\AboveTheFold\Database\Tables\AboveTheFold as ATFTable;
use WP_Rocket\Engine\Media\AboveTheFold\Database\Queries\AboveTheFold as ATFQuery;
use WP_Rocket\Engine\Media\AboveTheFold\Frontend\{Controller, Subscriber};

class ServiceProvider extends AbstractServiceProvider {
	/**
	 * The provides array is a way to let the container
	 * know that a service is provided by this service
	 * provider. Every service that is registered via
	 * this service provider must have an alias added
	 * to this array or it will be ignored.
	 *
	 * @var array
	 */
	protected $provides = [
		'atf_table',
		'atf_query',
		'atf_controller',
		'atf_subscriber',
	];

	/**
	 * Registers the classes in the container
	 *
	 * @return void
	 */
	public function register() {
		$this->getContainer()->share( 'atf_table', ATFTable::class );
		$this->getContainer()->add( 'atf_query', ATFQuery::class );

		$this->getContainer()->get( 'atf_table' );
		$this->getContainer()->add( 'atf_controller', Controller::class )
			->addArgument( $this->getContainer()->get( 'options' ) )
			->addArgument( $this->getContainer()->get( 'atf_query' ) );
		$this->getContainer()->share( 'atf_subscriber', Subscriber::class )
			->addArgument( $this->getContainer()->get( 'atf_controller' ) );
	}
}
