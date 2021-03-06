<?php

/*
 * This file is part of Laravel Auto Presenter.
 *
 * (c) Shawn McCool <shawn@heybigname.com>
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace McCool\LaravelAutoPresenter;

use Exception;
use McCool\LaravelAutoPresenter\Exceptions\MethodNotFound;

abstract class BasePresenter
{
    /**
     * The resource that is the object that was decorated.
     *
     * @var object|null
     */
    protected $wrappedObject = null;

    /**
     * Create a new presenter.
     *
     * @param object $resource
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->wrappedObject = $resource;
    }

    /**
     * Get the wrapped object.
     *
     * @return mixed
     */
    public function getWrappedObject()
    {
        return $this->wrappedObject;
    }

    /**
     * Magic method access initially tries for local fields, then defers to the
     * decorated object.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (method_exists($this, $key)) {
            return $this->{$key}();
        }

        return $this->wrappedObject->$key;
    }

    /**
     * Magic Method access for methods called against the presenter looks for a
     * method on the resource, or throws an exception if none is found.
     *
     * @param string $key
     * @param array  $args
     *
     * @throws \McCool\LaravelAutoPresenter\Exceptions\MethodNotFound
     *
     * @return mixed
     */
    public function __call($key, $args)
    {
        if (method_exists($this->wrappedObject, $key)) {
            return call_user_func_array([$this->wrappedObject, $key], $args);
        }

        throw new MethodNotFound(get_called_class(), $key);
    }

    /**
     * Is the key set on either the presenter or the wrapped object?
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        if (method_exists($this, $key)) {
            return true;
        }

        return isset($this->wrappedObject->$key);
    }

    /**
     * Get the wrapped object, cast to a string.
     */
    public function __toString()
    {
        return (string) $this->wrappedObject;
    }
}
