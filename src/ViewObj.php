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

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ViewObj
{
    /**
     * @var Repository
     */
    protected $config;
    /**
     * @var Factory
     */
    protected $factory;

    public function __construct(Factory $factory, Repository $config)
    {
        $this->config = $config;
        $this->factory = $factory;
    }

    /** @noinspection PhpDocSignatureInspection */
    /**
     * Prepare parameters for ViewObj::viewObj()
     *
     * @param object $object
     * @param string|array $template Either the name of the template
     *   (defaults to 'default') or an array of data to pass to the template
     * @param array|null $data The data to pass to the template
     * @return View
     */
    public function __invoke()
    {
        $args = func_get_args();

        # handle first arg
        if (!isset($args[0])) {
            throw new Exception(
                'Expected first argument to be an object but nothing was received'
            );
        }
        $object = $args[0];
        if (!is_object($object)) {
            throw new Exception(
                'Expected first argument to be an object but ' .
                gettype($object) . ' received'
            );
        }

        $tpl = 'default';
        $data = [];
        $alreadyAssignedData = false;

        # handle second arg
        if (isset($args[1])) {
            $tmp = $args[1];
            if (is_string($tmp)) {
                $tpl = $tmp;
            } elseif (is_array($tmp)) {
                $data = $tmp;
                $alreadyAssignedData = true;
            } else {
                throw new Exception(
                    'Expected second argument to be string or array but ' .
                    gettype($tmp) . ' received'
                );
            }
        }

        # handle third arg
        if (array_key_exists(2, $args)) {
            $tmp = $args[2];
            if (is_array($tmp)) {
                if ($alreadyAssignedData) {
                    throw new Exception(
                        'Already received an array as second argument, ' .
                        'cannot receive an array as third argument too'
                    );

                }
                $data = $tmp;
            } elseif (null === $tmp) {
                # Silently slurp them
            } else {
                throw new Exception(
                    'Expected third argument to an array but ' .
                    gettype($tmp) . ' received'
                );
            }

        }

        return $this->viewObj($object, $tpl, $data);
    }

    /**
     * Inspects the passed object and based on its clas hierarchy builds a view
     * path name (based on the `base_path` configuration and the $template
     * parameter) and passes the data to it.
     *
     * Note: the object itself is always passed as '$obj' to the template!
     *
     * @param $object
     * @param string $tpl Name of the object specific template
     *   (defaults to 'default')
     * @param array $data The actual data you want to pass to the template
     * @return View
     */
    public function viewObj($object, $tpl = 'default', array $data = [])
    {
        $data['obj'] = $object;
        $class = get_class($object);
        $view = $this->config->get('view-obj.base_path') .
            str_replace('\\', '.', $class) . ".$tpl";

        return $this->factory->make($view, $data);
    }
}
