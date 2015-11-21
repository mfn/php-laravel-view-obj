<?php namespace Mfn\Laravel\ViewObj;

/*
 * This file is part of https://github.com/mfn/php-laravel-view-obj
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Markus Fischer <markus@fischer.name>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class Provider extends ServiceProvider
{
    /**
     *
     */
    public function boot()
    {
        $this->publishes([$this->configPath() => config_path('view-obj.php')]);

        /** @var BladeCompiler $blade */
        $blade = $this
            ->app
            ->make('view')
            ->getEngineResolver()
            ->resolve('blade')
            ->getCompiler();

        $blade->directive('view_obj', function ($expression) {
            # Implementation note: `view_obj()` returns a `View` which
            # implements `__toString()`, that's why this works
            return "<?php echo view_obj{$expression}; ?>";
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'view-obj');

        $this->app->singleton(ViewObj::class);
    }

    /**
     * @return string
     */
    public function configPath()
    {
        return __DIR__ . '/../config/view-obj.php';
    }
}
