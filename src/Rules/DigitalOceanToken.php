<?php

namespace TomatoPHP\TomatoEddy\Rules;

use TomatoPHP\TomatoEddy\Infrastructure\DigitalOcean;
use TomatoPHP\TomatoEddy\Infrastructure\ProviderFactory;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class DigitalOceanToken implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('eddy.fake_credentials_validation')) {
            if (Str::startsWith($value, 'valid-')) {
                return;
            }

            $fail(__('The API token is invalid.'));

            return;
        }

        /** @var ProviderFactory */
        $providerFactory = app(ProviderFactory::class);

        /** @var DigitalOcean */
        $digitalOcean = $providerFactory->forCredentials(new Credentials([
            'provider' => Provider::DigitalOcean,
            'credentials' => ['digital_ocean_token' => $value],
        ]));

        if (! $digitalOcean->canConnect()) {
            $fail(__('The API token is invalid.'));
        }
    }
}
