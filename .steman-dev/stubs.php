<?php

/**
 * Laravel Global Function Stubs for IDE
 * This file is used for static analysis only.
 */

namespace {
    /** @return mixed */
    function config($key = null, $default = null) { return $default; }
    /** @return mixed */
    function env($key, $default = null) { return $default; }
    /** @return object */
    function collect($value = null) { return new class { public function join($glue) { return ''; } public function map($callback) { return $this; } public function take($n) { return $this; } public function get() { return []; } }; }
    /** @return mixed */
    function setting($key, $default = null) { return $default; }
    /** @return object */
    function request($key = null, $default = null) { return new class { public function fullUrl() { return ''; } public function all() { return []; } public function only($keys) { return []; } public function ip() { return ''; } }; }
    /** @return object */
    function app($abstract = null) { return new class { public function bound($a) { return false; } }; }
    /** @return object */
    function response($content = '', $status = 200, array $headers = []) { return new class { public function json($data = [], $status = 200, $headers = [], $options = 0) { return $this; } public function view($view, $data = [], $status = 200) { return $this; } }; }
    /** @return object */
    function now() { return new class { public function toIso8601String() { return ''; } }; }
    /** @return string */
    function storage_path($path = '') { return ''; }
    /** @return string */
    function route($name, $parameters = [], $absolute = true) { return ''; }
    /** @return string */
    function csrf_token() { return ''; }
    /** @return string */
    function asset($path) { return ''; }
    /** @return object */
    function view($view = null, $data = [], $mergeData = []) { return new class { public function with($key, $value = null) { return $this; } }; }
    /** @return object */
    function auth() { return new class { public function user() { return new class { public $name = ''; public $role = ''; }; } }; }
}

namespace Illuminate\Support {
    class Collection { 
        public function join($glue) { return ''; } 
        public function map($callback) { return $this; } 
        public function take($n) { return $this; } 
        public function slice($offset, $length = null) { return $this; }
        public function all() { return []; }
        public function get() { return []; }
    }
}

namespace Carbon {
    class Carbon { public static function parse($d) { return new static; } public function diffForHumans() { return ''; } }
}

namespace Illuminate\Database\Query {
    class Builder {}
}

namespace {
    /**
     * @mixin \Illuminate\Database\Eloquent\Model
     * @mixin \Illuminate\Database\Query\Builder
     */
    class Eloquent {}
}

namespace Illuminate\Database\Eloquent {
    class Model {
        /** @return static|$this */
        public static function orderBy($column, $direction = 'asc') { return new static; }
        /** @return static|$this */
        public static function take($value) { return new static; }
        /** @return static|$this */
        public static function limit($value) { return new static; }
        /** @return \Illuminate\Support\Collection|static[] */
        public function get($columns = ['*']) { return []; }
        /** @return static */
        public static function create(array $attributes = []) { return new static; }
        /** @return static|$this */
        public static function where($column, $operator = null, $value = null) { return new static; }
        /** @return static|$this */
        public static function latest() { return new static; }
        /** @return mixed */
        public function value($column) { return null; }
        /** @return static|null */
        public function first($columns = ['*']) { return new static; }
        public function update(array $attributes = [], array $options = []) { return true; }
        public function delete() { return true; }
    }
}

namespace App\Models {
    /**
     * @mixin \Illuminate\Database\Eloquent\Model
     * @mixin \Illuminate\Database\Query\Builder
     */
    class Major extends \Illuminate\Database\Eloquent\Model {}
}
