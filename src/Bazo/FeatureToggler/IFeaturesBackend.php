<?php

namespace Bazo\FeatureToggler;


/**
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
interface IFeaturesBackend
{

	public function getConfig();
}
