<?php

namespace Database\Factories;

use DateTime;

class FakerFallback
{
    public static function get()
    {
        if (class_exists('\\Faker\\Generator')) {
            try {
                return app(\\Faker\\Generator::class);
            } catch (\\Throwable $e) {
                // fall through to fallback
            }
        }

        return new self();
    }

    public function name()
    {
        return 'Name '.substr(md5((string)mt_rand()), 0, 6);
    }

    public function company()
    {
        return 'Company '.substr(md5((string)mt_rand()), 0, 6);
    }

    public function phoneNumber()
    {
        return '09'.mt_rand(100000000, 999999999);
    }

    public function address()
    {
        return 'Address '.substr(md5((string)mt_rand()), 0, 6);
    }

    public function bothify(string $pattern)
    {
        $result = $pattern;
        $result = preg_replace_callback('/#/', fn() => (string)mt_rand(0,9), $result);
        $result = preg_replace_callback('/\?/', fn() => chr(mt_rand(97,122)), $result);
        return $result;
    }

    public function numerify(string $pattern)
    {
        return $this->bothify($pattern);
    }

    public function unique()
    {
        return $this;
    }

    public function optional($weight = 0.5)
    {
        return $this;
    }

    public function randomElement(array $arr)
    {
        return $arr[array_rand($arr)];
    }

    public function numberBetween($min, $max)
    {
        return mt_rand($min, $max);
    }

    public function randomFloat($nbMaxDecimals, $min, $max)
    {
        $dec = pow(10, $nbMaxDecimals);
        return mt_rand($min * $dec, $max * $dec) / $dec;
    }

    public function paragraph()
    {
        return 'Paragraph '.substr(md5((string)mt_rand()), 0, 12);
    }

    public function sentence()
    {
        return 'Sentence '.substr(md5((string)mt_rand()), 0, 8);
    }

    public function words($num = 3)
    {
        $out = [];
        for ($i = 0; $i < $num; $i++) {
            $out[] = 'word'.mt_rand(1,999);
        }
        return $out;
    }

    public function dateTimeBetween($start, $end)
    {
        return new DateTime();
    }

    public function postcode()
    {
        return (string)mt_rand(1000, 9999);
    }

    public function boolean($chance = 50)
    {
        return mt_rand(1,100) <= $chance;
    }

    public function safeEmail()
    {
        return 'user'.mt_rand(1000,9999).'@example.com';
    }

    public function firstName()
    {
        return 'First'.mt_rand(1,999);
    }

    public function lastName()
    {
        return 'Last'.mt_rand(1,999);
    }

    public function nameMale()
    {
        return $this->name();
    }

    public function __call($method, $args)
    {
        // Return something generic for unknown methods
        if (str_contains($method, 'email')) {
            return $this->safeEmail();
        }

        if (str_contains($method, 'phone')) {
            return $this->phoneNumber();
        }

        return 'x'.substr(md5((string)mt_rand()), 0, 6);
    }
}
