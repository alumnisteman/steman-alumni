<?php

namespace Illuminate\Database\Eloquent {
    class Model {}
}

namespace Illuminate\Contracts\Queue {
    interface ShouldQueue {}
}

namespace Illuminate\Foundation\Bus {
    trait Dispatchable {}
}

namespace Illuminate\Queue {
    trait InteractsWithQueue {}
    trait SerializesModels {}
}

namespace Illuminate\Bus {
    trait Queueable {}
}

namespace Illuminate\Http {
    class Request {
        public function only($keys) { return []; }
        public function ip() { return ''; }
        public function header($key, $default = null) { return ''; }
    }
}

namespace Illuminate\Support\Facades {
    class Auth {
        public static function id() { return 1; }
    }
    class Cache {
        public static function remember($key, $ttl, $callback) { return $callback(); }
    }
}

namespace {
    function view($view = null, $data = [], $mergeData = []) {}
    function request($key = null, $default = null) { return new \Illuminate\Http\Request(); }
    function compact(...$vars) { return []; }
    function back($status = 302, $headers = [], $fallback = false) {}
}
