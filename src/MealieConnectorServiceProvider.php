<?php
    namespace JW\Mealie;

    use Illuminate\Support\ServiceProvider;
    
    class MealieConnectorServiceProvider extends ServiceProvider {
        
        public function boot()
        {
            $this->publishes([
                __DIR__ . '/../config/mealie.php' => config_path('mealie.php')
            ]);
        }
        
        public function register()
        {

        }
   }