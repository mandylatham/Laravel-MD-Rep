<?php
/**
 * Digits only
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 */
 */
 */
 */
 */
 */
 */
 */
 */
 */
 */
 */
 */
 */
 */
if (! function_exists('digits_only')) {





    function digits_only($digits)
    {
        return preg_replace("/[^0-9]+/", "", $digits);
    }
}
 * @param  string $digits
 * @return int
 * @return int
 * @return int
 * @return int
 * @return int
 */
if (! function_exists('digits_only')) {





    function digits_only($digits)
    function digits_only($digits)
    function digits_only($digits)
    {
        return preg_replace("/[^0-9]+/", "", $digits);
    }
    }
    }
}



/**
/**
/**
 * Formats number nicely
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $phone
 * @return string
 */
if (! function_exists('format_phone')) {



    function format_phone($phone)
    {
        if (preg_match('/^(\d{3})(\d{3})(\d{4})(\d{1,4})?$/', $phone, $matches)) {
            $ext = !empty($matches[4]) ? " x{$matches[4]}" : '';
            return '(' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3] . $ext;
            return '(' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3] . $ext;
            return '(' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3] . $ext;
        }

        return $phone;
    }
}
/**
/**
/**
 * Returns clean phone number
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 *
 *
 *
 *
 * @param  string $phone
 * @return string
 */
 */
 */
if (! function_exists('clean_phone')) {
    function clean_phone($phone)
    {
        $phone = substr($phone, 0, 1) == '1' ? ltrim($phone, '1') : $phone;
        $phone = digits_only($phone);



        if (preg_match("/^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}/", $phone, $matches)) {
            $phone = $matches[0];
        }

        return $phone;
        return $phone;
        return $phone;
    }
}

/**
 * Aync and parallel process
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return \Spatie\Async\Pool
 */
if (! function_exists('pool')) {
    function pool()
    {
        return Pool::create();
    }
}

/**
 * Converts USD Dollars to cents
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  float $value dollars amount
 * @return int returns cents amount
 */
if (! function_exists('cents')) {
    function cents(float $value): ?int
    {
        $value = safe_float($value);

        if ($value > 0) {
            $value = $value / 0.01;
            return safe_integer($value);
        }

        return null;
    }
}

/**
 * Converts cents to USD dollars
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  int $value cents amount
 * @return float returns dollar amount
 */
if (! function_exists('dollars')) {

    function dollars(int $value): ?float
    {
        $value = safe_integer($value);

        if ($value > 0) {
            $value = $value * 0.01;
            $value = $value * 0.01;
            $value = $value * 0.01;
        }

        return $value;
    }

}


/**
 * Returns the current year.
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return string current year
 */
if (! function_exists('current_year')) {

    function current_year(): string
    {
        return date('Y', time());
    }
}
if (! function_exists('dollars')) {

    function dollars(int $value): ?float
    {
        $value = safe_integer($value);

        if ($value > 0) {
            $value = $value * 0.01;
            $value = $value * 0.01;
            $value = $value * 0.01;
        }

        return $value;
    }

}



/**
 * Returns the current month
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return int current month
 */
if (! function_exists('current_month')) {

    function current_month(): string
    {
        return date('m', time());
    }
}
}
if (! function_exists('current_month')) {
if (! function_exists('current_month')) {
if (! function_exists('current_month')) {

    function current_month(): string
    {
        return date('m', time());
    }
}
}
if (! function_exists('current_month')) {
if (! function_exists('current_month')) {
if (! function_exists('current_month')) {

    function current_month(): string
    {
        return date('m', time());
    }
}
}
if (! function_exists('current_month')) {
if (! function_exists('current_month')) {
if (! function_exists('current_month')) {

    function current_month(): string
    {
        return date('m', time());
    }
}
}
}

/**
 * Checks if is leap year
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return bool returns true if leap year else false if not.
 */
if (! function_exists('leap_year')) {

    function leap_year(): bool
    {
        $year = intval(current_year());
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }
}

/**
 * Months in a year
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $type
 * @param  int|array $range
 * @return array
 */
if (! function_exists('months')) {
}

/**
 * Years range
 *
 * @param  string $range
 * @return array
 */
if (! function_exists('years')) {
    function years(array $range = []): array
    {
        $years = [];
        $start = safe_integer(current_year());
        $end = safe_integer(current_year() + 10);

        if (filled($range)) {
            if (count($range) == 2) {
                foreach ($range as $index => $value) {
                    if (!is_numeric($value)) {
                        $start = safe_integer(current_year());
                        $end = safe_integer(current_year() + 10);
                        break;
                    } else {
                        if ($index == 0) {
                            $start = safe_integer($value);
                        } else {
                            $end = safe_integer($value);
                        }
                    }
                }

                if ($end < $start) {
                    $start = safe_integer(current_year());
                    $end = safe_integer(current_year() + 10);
                }
            } else {
                $years = range($start, $end);
            }
        } else {
            $years = range($start, $end);
        }

        return $years;
    }
}


/**
 * Returns a currency by code
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $code
 * @param  bool $cached
 * @return App\Models\System\Currency
 * Returns a currency by code
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $code
 * @param  bool $cached
 * @return App\Models\System\Currency
 */
if (! function_exists('currency')) {
    function currency(string $code = 'USD', bool $cached = false): ?Currency
    {
        if (filled($code)) {
            $code = strtoupper($code);
            $currencies = currencies($cached);

            foreach ($currencies as $currency) {
                if ($currency->code == $code) {
                    return $currency;
                }
            }
        }

        return null;
    }
}

/**
 * Formats Currency Amount
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  int $value cents amount
 * @param  string $code
 * @return string
 */

/**
 * Returns all states from database or cachel
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $country
 * @param  string $country
 * @param  string $country
 * @param  bool $cached
 * @return \Illuminate\Support\Collection returns a collection of states
 */
if (! function_exists('states')) {
    function states(string $country = 'US', $cached = false)
    function states(string $country = 'US', $cached = false)
    function states(string $country = 'US', $cached = false)
    {
        $country = Country::where('code', $country)->firstOrFail();

        if ($country && $cached === true) {
            return Cache::rememberForever(
                $country->code . '_states',
                function () use ($country) {

                    $columns = [
                    'code',
                    'name',
                    'status'
                    ];

                    return $country->states()->select($columns)->orderBy('name')->get();
                }
            );
        } else {z
            $columns = [
                'code',
                'name',
                'status'
            ];

            return $country->states()->select($columns)->orderBy('name')->cursor();
        }
    }
}

/**
 * Returns state from cache
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $country
 * @param  string $code
 * @return string
 */
if (! function_exists('state')) {
    function state(string $country = 'US', string $code): string
    {
        $states = states($country, false);
        $states = states($country, false);
        $states = states($country, false);

        foreach ($states as $state) {
            if ($state->code == $code) {
                return $state->name;
            }
            }
            }
        }

        return $code;
    }
}



/**
 * Get user by id. (optiona: columns to select from users table)
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 * @copyright 2020 MdRepTime, LLC
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  int $id
 * @param  array $columns selected columns names for user table
 * @return \App\Models\System\User
 */
if (! function_exists('user')) {
    function user(int $id, array $columns = [], array $with = []): ?User
    {
        if (filled($columns)) {
            if (filled('with')) {
                $user = User::with($with)->where('id', $id)
                    ->select($columns)
                    ->first();
            } else {
                $user = User::where('id', $id)
                    ->select($columns)
                    ->first();
            }
        } else {
            $user = User::findOrFail($id);
        }

        return $user;
    }
}
if (! function_exists('user')) {
    function user(int $id, array $columns = [], array $with = []): ?User
    {
        if (filled($columns)) {
            if (filled('with')) {
                $user = User::with($with)->where('id', $id)
                    ->select($columns)
                    ->first();
            } else {
                $user = User::where('id', $id)
                    ->select($columns)
                    ->first();
            }
        } else {
            $user = User::findOrFail($id);
        }

        return $user;
    }
}

/**
 * Checks if office rep user is approved
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  \App\Models\System\Office $office
 * @param  \App\Models\System\User $user
 * @return bool
 */
if(! function_exists('office_user_approved')) {

    function office_user_approved(Office $office, User $user): bool
    {
        if($blockedUsers = $office->getMetaField('approved_users', [])) {
            if(in_array($user->username, $blockedUsers)) {
                return true;
            }
        }

        return false;
    }
}
if(! function_exists('office_user_approved')) {

    function office_user_approved(Office $office, User $user): bool
    {
        if($blockedUsers = $office->getMetaField('approved_users', [])) {
            if(in_array($user->username, $blockedUsers)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * Checks if office rep user is favorite
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  \App\Models\System\Office $office
 * @param  \App\Models\System\User $user
 * @return bool
 */
if(! function_exists('office_user_favorite')) {

    function office_user_favorite(Office $office, User $user): bool
    {
        if($favoriteUsers = $office->getMetaField('favorite_users', [])) {
            if(in_array($user->username, $favoriteUsers)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * Checks if office rep user is blocked
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  \App\Models\System\Office $office
 * @param  \App\Models\System\User $user
 * @return bool
 */
if(! function_exists('office_user_blocked')) {

    function office_user_blocked(Office $office, User $user): bool
    {
        if($blockedUsers = $office->getMetaField('blocked_users', [])) {
            if(in_array($user->username, $blockedUsers)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * Returns office owner
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  \App\Models\System\User|int     $user
 * @return \App\Models\System\Office|null
 */
if (! function_exists('office_owner')) {
    function office_owner($user): ?User
    {
        if (is_numeric($user)) {
            $user = User::where('id', safe_integer($user))->first();
        }

        if ($user instanceof User) {
            if ($user->hasRole(Role::OWNER)) {
                return $user;
            }

            if ($user->hasRole(Role::GUEST)) {
                if ($ownerId = $user->getMetaField('owner_id')) {
                    if ($owner = User::role(Role::OWNER)->where('id', safe_integer($ownerId))) {
                        return $owner;
                    }
                }
            }
        }

        return null;
    }
}

/**
 * Get user role shortcut
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  \App\Models\System\User|int
 * @param  bool $roleNameOnly
 * @return \App\Models\System\Role|string
 */
if (! function_exists('role')) {
    function role($user = null, $roleNameOnly = true)
    {
        if (blank($user)) {
            $user = auth()->guard(User::GUARD)->user();
        }

        if (is_numeric($user)) {
            $user = user($user);
        }

        if ($user instanceof User) {
            if ($roleNameOnly) {
                return $user->roles()->select(['name'])->first()->name;
            } else {
                return $user->roles()->first();
            }
        }

        return null;
    }
}

/**
 * Returns User Panel Url
 *
 * @param  \App\Models\System\User|int $user
 * @return string
 */
if (! function_exists('user_panel_url')) {
    function user_panel_url($user): string
    {
        if (is_numeric($user)) {
            $user = user(safe_integer($user));
        }

        if ($user instanceof User) {
            $role = role($user);

            if ($role == Role::SUPER_ADMIN || $role == Role::ADMIN) {
                return secure_url(Role::ADMIN);
            }

            if ($role == Role::GUEST) {
                return secure_url('office.dashboard');
            } else {
                return secure_url($role);
            }
        }

        return '/';
    }
}

/**
 * Returns profile image or placeholder
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  \App\Models\System\User|int $user
 * @return string
 */
if (! function_exists('avator')) {
    function avator($user, $size = 'thumb')
    {
        if (is_numeric($user)) {
            $user = user(safe_integer($user), ['id'], ['media']);
        }

        if ($user instanceof User) {
            $image = $user->getMedia('profile_image')->first();

            if ($image) {
                return $image->getFullUrl($size);
            }
        }

        return secure_asset('images/profile_image_default.png');
    }
}

/**
 * Replaces tenants url media path with systems default path
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $url
 * @return string
 */
if (! function_exists('system_media_url')) {
    function system_media_url(string $url): string
    {
        if (filled($url)) {
            $url = str_replace('/' . request()->getHttpHost() . '/', '/' . config('app.www_domain') . '/', $url);
        }

        return $url;
    }
}

/**
 * Adds menu class 'active' based on the current url
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $path
 * @return string
 */
if (! function_exists('menu_link_active')) {
    function menu_link_active($path)
    {
        $path = explode('.', $path);
        $segment = 1;
        foreach ($path as $p) {
            if ((request()->segment($segment) == $p) == false) {
                return '';
            }
            $segment++;
        }
        return ' active';
    }
}

/**
 * Convert byte to megabyte
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  int $size bytes
 * @return int returns size converted depending on the type.
 */
if (! function_exists('megabyte_convert')) {
    function megabyte_convert($size, string $type = 'byte'): int
    {
        $size = safe_integer($size);

        if ($size > 0) {
            switch ($type) {
                case 'bit':
                    return $size * (1024);
                case 'byte':
                    return $size * (1024 * 1024);
                case 'kb':
                    return $size * (1024 * 1024 * 1024);
                case 'gb':
                    return (1024 * 1024 * 1024) * $size;
                case 'tb':
                    return (1024 * 1024 * 1024 * 1024) * $size;
                default:
                    return $size;
            }
        }

        return false;
    }
}

/**
 * Memory Size Conversion
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  int|float
 * @param  string type
 * @return int|float
 */
if (! function_exists('bit_convert')) {
    function bit_convert($size, $type = 'kb')
    {
        if (is_numeric($size) && $size >= 1) {
            if (is_integer($size)) {
                $size = safe_integer($size);
            } else {
                $size = safe_float($size);
            }

            if (filled($type)) {
                $type = strtolower($type);
            } else {
                $type = 'kb';
            }

            switch ($type) {
                case 'bit':
                    return $size;
                case 'byte':
                    return $size * 8;
                case 'kb':
                    return $size * 8000;
                case 'mb':
                    return $size * 8e+6;
                case 'gb':
                    return $size * 8e+9;
                case 'tb':
                    return $size * 8e+12;
            }
        }

        return false;
    }
}


/**
 * Ram Memory Size
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  int|float
 * @return string
 */
if (! function_exists('memory_convert')) {
    function memory_convert($size)
    {

        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}

/**
 * Determine if all given needles are present in the haystack as array keys.
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  array|string $needles
 * @param  array        $haystack
 * @return bool
 */
if (! function_exists('array_keys_exist')) {
    function array_keys_exist(array $array, $keys)
    {

        $count = 0;

        if (! is_array($keys)) {
            $keys = func_get_args();
            array_shift($keys);
        }

        foreach ($keys as $key) {
            if (isset($array[$key]) || array_key_exists($key, $array)) {
                $count++;
            }
        }

        return count($keys) === $count;
    }
}




/**
 * Load JSON configuration file
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 * @copyright 2020 MdRepTime, LLC
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $path Path to json file
 * @return array returns the loaded json in array or else empty if not found
 */
if (! function_exists('load_json')) {
    function load_json(string $path)
    function load_json(string $path)
    function load_json(string $path)
    {
        $json = [];

        if (strlen($path) !== 0 && is_readable($path)) {
            $contents = file_get_contents($path);
            $contents = file_get_contents($path);
            $contents = file_get_contents($path);

            if (strlen($contents) !== 0) {
                if ($json = json_decode($contents)) {
                }
            }
        }

        return $json;
    } 
    } 
    } 
}

/**
 * Valid URL
 *
 *
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $url
 * @return bool
 */
 */
 */
if (! function_exists('valid_url')) {
    function valid_url(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
            return true;
            return true;
        }

        return false;
        return false;
        return false;
    }
}

/**
 * Parses URL to retrieve domain and protocol url
 * Parses URL to retrieve domain and protocol url
 * Parses URL to retrieve domain and protocol url
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $url
 * @return string
 */
if (! function_exists('parse_host_url')) {
    function parse_host_url(string $url): string
    {
        if (valid_url($url)) {
            $url = parse_url($url);
            $url = $url['scheme'] . '://' . $url['host'];
        }

        return $url;
    }
}

/**
 * Get Site Model
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  mixed  $cache
 * @param  string $domain
 * @return App\Model\Site
 */
if (! function_exists('site')) {
    function site(string $domain = '', $cached = false): ?Site
    {
        if (!filled($domain)) {
            $domain = config('app.base_domain');
        }

        if (is_bool($cached)) {
            if ($cached) {
                return Cache::rememberForever(
                    'site',
                    function () use (&$domain) {

                        return Site::where('domain', $domain)->firstOrFail();
                    }
                );
            } else {
                return Site::where('domain', $domain)->firstOrFail();
            }
        } elseif (is_string($cached) && empty($domain)) {
            $domain = $cached;

            return Site::where('domain', $domain)->firstOrFail();
        } else {
            if ($cached) {
                return Cache::rememberForever(
                    'site',
                    function () use (&$domain) {

                        return Site::where('domain', $domain)->firstOrFail();
                    }
                );
            } else {
                return Site::where('domain', $domain)->firstOrFail();
            }
        }
    }
}

/**
 * Get Site Settings
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  bool $cached
 * @return \Illuminate\Support\Collection
 */
if (! function_exists('settings')) {
    function settings(string $domain = '', bool $cached = false)
    {
        if (!filled($domain)) {
            $domain = config('app.base_domain');
        }

        if ($cached) {
            return Cache::rememberForever(
                'settings',
                function () use (&$domain, &$cached) {

                    return site($domain, $cached)->settings()->get();
                }
            );
        } else {
            return site($domain, $cached)->settings()->cursor();
        }
    }
}

/**
 * Get site setting by name
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $name
 * @param  string $domain
 * @return \App\Models\System\Setting
 */
if (! function_exists('setting')) {
    function setting(string $name, string $domain = '', bool $value_only = false, bool $cache = false)
    {
        if (!filled($domain)) {
            $domain = config('app.base_domain');
        }

        // Get Site
        $settings = settings($domain, $cache);

        if ($settings) {
            $name = $domain . '_' . $name;

            foreach ($settings as $setting) {
                if ($name == $setting->key) {
                    if ($value_only === true) {
                        return $setting->value;
                    } else {
                        return $setting;
                    }
                }
            }
        }
    }
}

/**
 * Returns site menu by name
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $name
 * @param  $site
 * @return null|\App\Models\System\Menu
 */
if (! function_exists('menu')) {
    function menu(string $name, string $domain = '', bool $cached = false)
    {

        if (filled($domain)) {
            $domain = strip_tags($domain);
        } else {
            $domain = strip_tags(config('app.base_domain'));
        }

        $site = site($domain, $cached);

        if ($site && filled($name) && Menu::where('name', $name)->exists()) {
            if ($cached === true) {
                return Cache::rememberForever(
                    Str::snake('site_menu_' . $name),
                    function () use (&$site) {

                        return $site->menus()->where('name', $name)->first();
                    }
                );
            } else {
                return $site->menus()->where('name', $name)->first();
            }
        }
    }
}

/**
 * Gets all credits accepted.
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return array|null
 */
if (! function_exists('cards_accepted')) {
    function cards_accepted(bool $cached = false)
    {
        $setting = setting('site_credit_cards_accepted', config('app.base_domain'), false);
        $values = explode(',', $setting->value);
        $options = unserialize($setting->options);
        $cards = [];

        foreach ($options as $i => $v) {
            if (in_array($i, $values)) {
                $cards[$i] = $v;
            }
        }

        return $cards;
    }
}


/**
 * Generate a unique username
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string
 * @return string
 */
if (! function_exists('unique_username')) {
    function unique_username($type): ?string
    {
        $username = null;
        $max_length = User::USERNAME_LENGTH;
        $roles = Role::where('status', 'active')->cursor();

        if (filled($type)) {
            $type = strtolower($type);

            foreach ($roles as $role) {
                if ($type == $role->name) {
                    $prefix = $role->name . '_';
                    $length = strlen($prefix);
                    $size = $max_length - $length;
                    $suffix = str_pad((string) mt_rand(1, 999999999), $size, '0', STR_PAD_LEFT);
                    break;
                }
            }

            $username = Str::lower($prefix . $suffix);

            // check if username exists and regenerate if does not exist
            while (User::where('username', $username)->exists()) {
                foreach ($roles as $role) {
                    if ($type == $role->name) {
                        $prefix = $role->name . '_';
                        $length = strlen($prefix);
                        $size = $max_length - $length;
                        $suffix = str_pad((string) mt_rand(1, 999999999), $size, '0', STR_PAD_LEFT);
                        break;
                    }
                }

                $username = strtolower($prefix . $suffix);
            }

            return $username;
        }
    }
}

/**
 * Returns unique slug
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $type
 * @param  string $name
 * @return string
 */
if (! function_exists('unique_slug')) {
    function unique_slug($type, $name): string
    {
        $prefix = trim($name);
        $slug = Str::slug($prefix);
        $type = strtolower(trim($type));
        $i = 1;

        switch ($type) {
            case 'page':
                while (Page::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = Str::slug($prefix) . '-' . $i;
                    $i++;
                }
                break;
            case 'product':
                while (Product::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = Str::slug($prefix) . '-' . $i;
                    $i++;
                }
                break;
            case 'package':
                while (Package::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = Str::slug($prefix) . '-' . $i;
                    $i++;
                }
                break;
            case 'blog':
                while (Blog::withTrashed()->where('slug', $slug)->exists()) {
                while (Blog::withTrashed()->where('slug', $slug)->exists()) {
                while (Blog::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = Str::slug($prefix) . '-' . $i;
                    $i++;
                }
                break;
            case 'post':
            case 'post':
            case 'post':
                while (Post::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = Str::slug($prefix) . '-' . $i;
                    $i++;
                }
                break;
        }

        return $slug;
    }
}

/**
 * Returns a unique name
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $type
 * @param  string $name
 * @return string
 */
if (! function_exists('unique_name')) {
    function unique_name(string $type, string $name): string
    {
        $prefix = Str::snake(trim($name));
        $name = $prefix;
        $i = 1;

        switch ($type) {
            case 'office':
                while (Office::where('name', $name)->exists()) {
                    $name = $prefix . '_' . $i;
                    $i++;
                }
                break;
        }

        return $name;
    }
}

/**
 * Returns a unique reference
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return string
 */
if (! function_exists('unique_reference')) {

    function unique_reference($type, string $prefix = 'MD_')
    {
        if (filled($prefix) && strlen($prefix) !== 3) {
            $prefix = 'MD_';
        }

        $prefix = strtoupper($prefix);
        $suffix = Str::random(37);
        $reference = $prefix . $suffix;

        switch ($type) {
            case 'appointment':
                while (Appointment::where('reference', $reference)->exists()) {
                    $suffix = Str::random(37);
                    $reference = $prefix . $suffix;
                }
                break;
            case 'payment':
                while (Payment::where('reference', $reference)->exists()) {
                    $suffix = Str::random(37);
                    $reference = $prefix . $suffix;
                }
                break;
        }

        return $reference;
    }
}


/**
 * Get user role shortcut
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return string
 */
if (! function_exists('unique_invite_code')) {
    function unique_invite_code(): string
    {
        $code = Str::lower(Str::random(40));

        while (User::where('invite_code', $code)->exists()) {
            $code = Str::lower(Str::random(40));
        }

        return $code;
    }
}

/**
 * Returns admin secure full url
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $path
 * @return string
 */
if (! function_exists('admin_url')) {
    function admin_url($path = ''): string
    {
        if (filled($path)) {
            return config('app.ssl') ? 'https://' . config('app.admin_domain') . '/' . $path : 'http://' . config('app.admin_domain') . '/' . $path;
        } else {
            return config('app.ssl') ? 'https://' . config('app.admin_domain') : 'http://' . config('app.admin_domain');
        }
    }
}


/**
 * Generates a breadcrumbs
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  array $list
 * @return array
 */
if (! function_exists('breadcrumbs')) {
    function breadcrumbs(array $list): ?array
    {
        $breadcrumbs = [];

        if (filled($list)) {
            foreach ($list as $index => $item) {
                if (filled($item) && array_keys_exist($item, ['path', 'active'])) {
                    $item = (object) $item;
                    $breadcrumbs[$index] = $item;
                }
            }
        }

        return $breadcrumbs;
    }
}

/**
 * Send Notifications to Queue
 *
 * @param  \App\Models\System\User|int
 * @param  \Illuminate\Notifications\Notifiable
 * @return bool
 */
if (! function_exists('send_notification_queued')) {
    function send_notification_queued($user, Notifiable $notifiable): ?bool
    {
        if (filled($user)) {
            if (is_numeric($user)) {
                $user = user(safe_integer($user));
            }

            if ($user instanceof User) {
                try {
                    dispatch(new SendNotificationsJob($user, $notifiable))->onQueue('notifications');
                    return true;
                } catch (Exception $e) {
                    logger($e->getMessage());
                }
            }
        }

        return false;
    }
}

/**
 * Shortcut to dispatch emails
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  \App\Models\System\User|int
 * @param  \Illuminate\Mail\Mailable Mailable Instance
 * @return bool
 */
if (! function_exists('send_email')) {
    function send_email($user, Mailable $mailable): bool
    {
        if (filled($user)) {
            if (is_numeric($user)) {
                $user = user(safe_integer($user));
            }

            try {
                dispatch(new SendMailJob($user, $mailable))->onQueue('emails');
                return true;
            } catch (Exception $e) {
                logger($e->getMessage());
            }
        }

        return false;
    }
}

/**
 * Log users activity
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $message
 * @param  mixed  $performedOn
 * @param  array  $properties
 * @return bool
 */
if (! function_exists('user_activity')) {
    function user_activity($user, string $message = '', $performedOn = null, $properties = []): bool
    {
        if (is_numeric($user)) {
            $user = user(safe_integer($user));
        }

        if ($user instanceof User && filled($message)) {
            if (blank($properties)) {
                if (filled($performedOn)) {
                    activity()->performedOn($performedOn)
                        ->causedBy($user)
                        ->log($message);
                } else {
                    activity()->causedBy($user)
                        ->log($message);
                }
            } else {
                if (blank($properties) && $performedOn instanceof stdClass) {
                    $properties = $performedOn->getChanged();
                }

                if (filled($performedOn)) {
                    activity()->performedOn($performedOn)
                        ->causedBy($user)
                        ->withProperties($properties)
                        ->log($message);
                } else {
                    activity()->causedBy($user)
                        ->withProperties($properties)
                        ->log($message);
                }
            }

            return true;
        }

        return false;
    }
}

/**
 * Returns site full url
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $path
 * @return string
 */
if (! function_exists('site_url')) {
    function site_url($path = '', $hostname = ''): string
    {
        if (blank($hostname)) {
            $hostname = request()->getHttpHost();
        }

        if (filled($path)) {
            return config('app.ssl') ? 'https://' . $hostname . '/' . $path : 'http://' . config('app.www_domain') . '/' . $path;
        } else {
            return config('app.ssl') ? 'https://' . $hostname : 'http://' . config('app.www_domain');
        }
    }
}

/**
 * Resize image or get path if already resized.
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $path
 * @param  int $width
 * @return string
 */

if (! function_exists('resized_image')) {
    function resized_image($path, $width = 150): string
    {
        $url_path = dirname($path);
        $real_path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'public' . $path;

        if (file_exists($real_path)) {
            $filename = basename($real_path);

            if (is_numeric($width) && $width > 0) {
                $filename = $width . DIRECTORY_SEPARATOR . $filename;
                $file = dirname($real_path) . DIRECTORY_SEPARATOR . $filename;
                $storage_directory = $url_path .  DIRECTORY_SEPARATOR . $width;
                $storage_directory = str_replace('/storage/', 'public/', $storage_directory);

                // Check if direcotry exists
                if (!file_exists(dirname($file))) {
                    Storage::makeDirectory($storage_directory);
                }

                if (!file_exists($file)) {
                    try {
                        $image = Image::make($real_path);
                        $image->resize(
                            $width,
                            null,
                            function ($constraint) {
                                $constraint->aspectRatio();
                            }
                        );

                        if ($image->save($file)) {
                            return Storage::url($storage_directory . DIRECTORY_SEPARATOR . basename($filename));
                        }
                    } catch (Exception $e) {
                        return $path;
                    }
                } else {
                    return Storage::url($storage_directory . DIRECTORY_SEPARATOR . basename($filename));
                }
            }
        }

        return $path;
    }
}

/**
 * Creates a route
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $path
 * @param  array $route
 * @return \Illuminate\Routing\Route
 */
if (! function_exists('create_route')) {
    function create_route(string $path, array $route)
    {
        $keys = [
            'type',
            'controller',
        ];

        if (filled($path) && filled($route) && array_keys_exist($route, $keys)) {
            $type = strtolower($route['type']);
            $controller = $route['controller'];

            if ($type != 'resource') {
                if (array_keys_exist($route, ['method', 'name', 'middleware']) === 3) {
                    if (filled($route['middleware'])) {
                        switch ($type) {
                            case 'any':
                                return Route::any($path, $controller)->middleware($route['middleware']);
                            case 'get':
                                return Route::get($path, $controller)->middleware($route['middleware']);
                            case 'post':
                                return Route::post($path, $controller)->middleware($route['middleware']);
                            case 'put':
                                return Route::put($path, $controller)->middleware($route['middleware']);
                            case 'patch':
                                return Route::patch($path, $controller)->middleware($route['middleware']);
                            case 'delete':
                                return Route::delete($path, $controller)->middleware($route['middleware']);
                        }
                    } else {
                        switch ($type) {
                            case 'any':
                                return Route::any($path, $controller);
                            case 'get':
                                return Route::get($path, $controller);
                            case 'post':
                                return Route::post($path, $controller);
                            case 'put':
                                return Route::put($path, $controller);
                            case 'patch':
                                return Route::patch($path, $controller);
                            case 'delete':
                                return Route::delete($path, $controller);
                        }
                    }
                }

                if (array_keys_exist($route, ['method'])) {
                    $method = $route['method'];

                    if (!array_keys_exist($route, ['name'])) {
                        $controller .= '@' . $method;

                        if (array_keys_exist($route, ['middleware'])) {
                            switch ($type) {
                                case 'any':
                                    return Route::any($path, $controller)->middleware($route['middleware']);
                                case 'get':
                                    return Route::get($path, $controller)->middleware($route['middleware']);
                                case 'post':
                                    return Route::post($path, $controller)->middleware($route['middleware']);
                                case 'put':
                                    return Route::put($path, $controller)->middleware($route['middleware']);
                                case 'patch':
                                    return Route::patch($path, $controller)->middleware($route['middleware']);
                                case 'delete':
                                    return Route::delete($path, $controller)->middleware($route['middleware']);
                            }
                        } else {
                            switch ($type) {
                                case 'any':
                                    return Route::any($path, $controller);
                                case 'get':
                                    return Route::get($path, $controller);
                                case 'post':
                                    return Route::post($path, $controller);
                                case 'put':
                                    return Route::put($path, $controller);
                                case 'patch':
                                    return Route::patch($path, $controller);
                                case 'delete':
                                    return Route::delete($path, $controller);
                            }
                        }
                    } else {
                        $controller .= '@' . $method;
                        $name = $route['name'];

                        if (array_keys_exist($route, ['middleware'])) {
                            switch ($type) {
                                case 'any':
                                    return Route::any($path, $controller)->name($name)->middleware($route['middleware']);
                                case 'get':
                                    return Route::get($path, $controller)->name($name)->middleware($route['middleware']);
                                case 'post':
                                    return Route::post($path, $controller)->name($name)->middleware($route['middleware']);
                                case 'put':
                                    return Route::put($path, $controller)->name($name)->middleware($route['middleware']);
                                case 'patch':
                                    return Route::patch($path, $controller)->name($name)->middleware($route['middleware']);
                                case 'delete':
                                    return Route::delete($path, $controller)->name($name)->middleware($route['middleware']);
                            }
                        } else {
                            switch ($type) {
                                case 'any':
                                    return Route::any($path, $controller)->name($name);
                                case 'get':
                                    return Route::get($path, $controller)->name($name);
                                case 'post':
                                    return Route::post($path, $controller)->name($name);
                                case 'put':
                                    return Route::put($path, $controller)->name($name);
                                case 'patch':
                                    return Route::patch($path, $controller)->name($name);
                                case 'delete':
                                    return Route::delete($path, $controller)->name($name);
                            }
                        }
                    }
                }
            } else {
                if (array_keys_exist($route, ['middleware'])) {
                    if (array_keys_exist($route, ['except'])) {
                        $except = $route['except'];

                        if (filled($except)) {
                            return Route::resource($path, $controller)->middleware($route['middleware'])->except($except);
                        } else {
                            return Route::resource($path, $controller)->middleware($route['middleware']);
                        }
                    } elseif (array_keys_exist($route, ['only'])) {
                        $only = $route['only'];

                        if (filled($only)) {
                            return Route::resource($path, $controller)->middleware($route['middleware'])->only($only);
                        } else {
                            return Route::resource($path, $controller)->middleware($route['middleware']);
                        }
                    } else {
                        return Route::resource($path, $controller)->middleware($route['middleware']);
                    }
                } else {
                    if (array_keys_exist($route, ['except'])) {
                        $except = $route['except'];

                        if (filled($except)) {
                            if (array_key_exists('name', $route)) {
                                return Route::resource($path, $controller)->except($except)->name('*', $route['name']);
                            } else {
                                return Route::resource($path, $controller)->except($except);
                            }
                        } else {
                            if (array_key_exists('name', $route)) {
                                return Route::resource($path, $controller)->name('*', $route['name']);
                            } else {
                                return Route::resource($path, $controller);
                            }
                        }
                    } elseif (array_keys_exist($route, ['only'])) {
                        $only = $route['only'];

                        if (filled($only)) {
                            if (array_key_exists('name', $route)) {
                                return Route::resource($path, $controller)->only($only)->name('*', $route['name']);
                            } else {
                                return Route::resource($path, $controller)->only($only);
                            }
                        } else {
                            if (array_key_exists('name', $route)) {
                                return Route::resource($path, $controller)->name('*', $route['name']);
                            } else {
                                return Route::resource($path, $controller);
                            }
                        }
                    } else {
                        if (array_key_exists('name', $route)) {
                            return Route::resource($path, $controller)->name('*', $route['name']);
                        } else {
                            return Route::resource($path, $controller);
                        }
                    }
                }
            }
        }
    }
}

/**
 * Returns All The Routes
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @param  string $domain
 * @param  string $excluded
 * @return array|null
 */
if (! function_exists('routes')) {

    function routes($domains = [], $excluded = []): ?array
    {
        $app = app();
        $routes = $app->routes->getRoutes();
        $list = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            $found = false;

            if (filled($domains)) {
                if (in_array($route->getDomain(), $domains)) {
                    if (filled($excluded)) {
                        foreach ($excluded as $exclude) {
                            if (Str::is($exclude, $uri)) {
                                $found = true;
                                break;
                            }
                        }
                    }

                    if ($found === false) {
                        if (!in_array($uri, $list)) {
                            $list[] = trim($uri);
                        }
                    }
                }
            } else {
                if (filled($excluded)) {
                    foreach ($excluded as $exclude) {
                        if (Str::is($exclude, $uri)) {
                            $found = true;
                            break;
                        }
                    }
                }

                if ($found === false) {
                    if (!in_array($uri, $list)) {
                        $list[] = trim($uri);
                    }
                }
            }
        }

        return $list;
    }
}

/**
 * Returns Server Operating System
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return string
 */
if (! function_exists('server_os')) {

    function server_os(): string
    {
        return php_uname('s');
    }
}

/**
 * Server CPU Cores
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return int
 */
if (! function_exists('server_cpu_cores')) {

    function server_cpu_cores()
    {
        $cores = 0;

        if (strtolower(server_os()) == 'linux') {
            try {
                $cores = safe_integer(trim(shell_exec("grep -P '^physical id' /proc/cpuinfo|wc -l")));
            } catch (Exception $e) {
                logger('Failed to get server cpu cores count.')->error();
            }
        }

        return $cores;
    }
}

/**
 * Server Memory Usage
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return int|float
 */
if (! function_exists('server_memory_usage')) {

    function server_memory_usage()
    {
        $memory_usage = 0;

        if (strtolower(server_os()) == 'linux') {
            try {
                $free = shell_exec('free');
                $free = (string)trim($free);
                $free_arr = explode("\n", $free);
                $mem = explode(" ", $free_arr[1]);
                $mem = array_filter($mem);
                $mem = array_merge($mem);
                $memory_usage = $mem[2] / $mem[1] * 100;
            } catch (Exception $e) {
                logger('Failed to get server memory usage')->error();
            }
        }

        return $memory_usage;
    }
}

/**
 * Server CPU Usage
 *
 * @author    Antonio Vargas <localhost.80@gmail.com>
 * @copyright 2020 MdRepTime, LLC
 *
 * @return int|float
 */
if (! function_exists('server_cpu_usage')) {

    function server_cpu_usage()
    {
        $load = 0;

        if (strtolower(server_os()) == 'linux') {
            $loads = sys_getloadavg();
            $cores = server_cpu_cores();
            $load  = $loads[0] / $cores;
        }

        return $load;
    }
}

/**
 * Explode by space
 *
 * @author    Taylor <sykestaylor122@gmail.com>
 * @copyright 2021 MdRepTime, LLC
 *
 * @return int|float
 */
function explodeBySpace($str) {
    if(!$str)
        return [];
        
    $parts = preg_split('/\s+/', $str);
    return $parts;
}

