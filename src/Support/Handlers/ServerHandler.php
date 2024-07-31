<?php

namespace Somecode\Restify\Support\Handlers;

use _PHPStan_18cddd6e5\Nette\DI\InvalidConfigurationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Somecode\OpenApi\Builder;
use Somecode\OpenApi\Entities\Server\Server;
use Somecode\OpenApi\Entities\Server\Variable;
use Somecode\Restify\Support\Interfaces\Applieble;

class ServerHandler implements Applieble
{
    public function __invoke(Builder $builder): Builder
    {
        return $this->apply($builder);
    }

    public function apply(Builder $builder): Builder
    {
        $servers = $this->validateAndGetServersFromConfig();

        if ($servers->isNotEmpty()) {
            $builder->addServers($servers->toArray());
        }

        return $builder;
    }

    /**
     * @return Collection<Server[]>
     *
     * @throws InvalidConfigurationException
     */
    private function validateAndGetServersFromConfig(): Collection
    {
        $servers = collect();

        $serversFromConfig = config('restify.servers');

        if (! is_array($serversFromConfig)) {
            return $servers;
        }

        foreach ($serversFromConfig as $server) {
            if (! $this->validateServerRequiredFields($server)) {
                throw new InvalidConfigurationException('Server url is required');
            }

            $serverInstance = Server::create($server['url']);

            if (Arr::has($server, 'description') && is_string($server['description'])) {
                $serverInstance->description($server['description']);
            }

            $this->applyVariablesIfExists($server, $serverInstance);

            $servers->push($serverInstance);
        }

        return $servers;
    }

    private function validateServerRequiredFields(array $server): bool
    {
        return Arr::has($server, 'url');
    }

    private function applyVariablesIfExists(array $server, Server $instance): void
    {
        if (Arr::has($server, 'variables') && is_array($server['variables'])) {
            $variables = Arr::map($server['variables'], function ($variable) {
                if (! Arr::has($variable, 'name')) {
                    throw new InvalidConfigurationException('Server variable name is required');
                }

                $variableInstance = Variable::create($variable['name']);

                if (! Arr::has($variable, 'enum')) {
                    throw new InvalidConfigurationException('Server variable enum is required');
                }

                $variableInstance->enum($variable['enum']);

                if (Arr::has($variable, 'default')) {
                    $variableInstance->default($variable['default']);
                }

                if (Arr::has($variable, 'description')) {
                    $variableInstance->description($variable['description']);
                }

                return $variableInstance;
            });

            $variables = collect($variables);

            if ($variables->isNotEmpty()) {
                $instance->addVariables($variables->toArray());
            }
        }
    }
}
