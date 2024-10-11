<?php

namespace Pharaonic\Laravel\Translatable\Tests;

use Pharaonic\Laravel\Translatable\TranslatableServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [TranslatableServiceProvider::class];
    }
}
