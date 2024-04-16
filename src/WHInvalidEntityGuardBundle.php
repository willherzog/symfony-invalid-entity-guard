<?php

namespace WHSymfony\WHInvalidEntityGuardBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use WHSymfony\WHInvalidEntityGuardBundle\Validator\InvalidEntityGuardValidator;

/**
 * @author Will Herzog <willherzog@gmail.com>
 */
class WHInvalidEntityGuardBundle extends AbstractBundle
{
	protected string $extensionAlias = 'wh_invalid_entity_guard';

	public function configure(DefinitionConfigurator $definition): void
	{
		$definition->rootNode()
			->children()
				->arrayNode('exclude')
					->scalarPrototype()->end()
				->end()
			->end()
		;
	}

	public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$container->services()
			->set('whinvalidentityguard.validator', InvalidEntityGuardValidator::class)
				->decorate('validator')
				->args([
					service('whinvalidentityguard.validator.inner'),
					service('doctrine'),
					$config['exclude']
				])
		;
	}
}
