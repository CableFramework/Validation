<?php
namespace Cable\Validation;


use Cable\Container\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{

    /**
     * register new providers or something
     *
     * @return mixed
     */
    public function boot(){}

    /**
     * register the content
     *
     * @return mixed
     */
    public function register()
    {
        $this->getContainer()->add('validation', Validation::class);
        $this->getContainer()->alias(Validation::class, 'validation');
    }
}
