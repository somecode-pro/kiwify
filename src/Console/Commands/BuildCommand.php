<?php

namespace Somecode\Restify\Console\Commands;

use Illuminate\Console\Command;
use Somecode\Restify\Restify;

class BuildCommand extends Command
{
    protected $signature = 'restify:build';

    protected $description = 'Generates a specification file';

    public function handle(): void
    {
        Restify::build();
    }
}
