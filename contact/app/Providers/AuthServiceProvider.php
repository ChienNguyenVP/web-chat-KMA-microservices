<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;
use Illuminate\Support\Facades\Http;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->app['auth']->viaRequest('access_token', function ($request) {

            if($request->header('authorization', '')){
                // check redis -> có user hay chưa
                
                /*
                 $request->header('authorization', '') => [
                     'id' => 1,
                     'name' => 'chien'
                  ]
                */
                // nếu chưa thì đến account để lấy userinfo
                // dung cái auth token gọi đến api account láy thông tin
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => $request->header('authorization', '')
                ])->get('http://api.webchat.com:8000/user');
                
                if($response->status() == 200){
                    $userInfo = $response->json();
                    $user =  new User($userInfo);
                    // add vào redis
                    return $user;
                } 
            }
        });    
    }
}
