<?php 

namespace ApiArchitect\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use ApiArchitect\Auth\Console\Commands\CreateRoleCommand;

/**
 * Class CreateRoleCommandServiceProvider
 *
 * @package App\Providers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class CreateRoleCommandServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.create:role', function()
        {
            return new CreateRoleCommand();
        });

        $this->commands(
            'command.create:role'
        );
    }
}