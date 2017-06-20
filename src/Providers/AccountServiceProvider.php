<?php 

namespace ApiArchitect\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use ApiArchitect\Auth\Services\AccountService;

/**
 * Class AccountServiceProvider
 *
 * @package App\Providers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class AccountServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function boot()
    {
      $this->app['apiarchitect.account.service'] = new AccountService(
        $this->app['em']->getRepository(\ApiArchitect\Auth\Entities\User::class
        )
      );
    }
}