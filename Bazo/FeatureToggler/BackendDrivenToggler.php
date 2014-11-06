<?php

namespace Bazo\FeatureToggler;


/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class BackendDrivenToggler extends Toggler
{

	public function __construct(IFeaturesBackend $backend)
	{
		$config = $backend->getConfig();
		parent::__construct($config);
	}


}
