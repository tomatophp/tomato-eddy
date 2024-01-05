<?php

namespace TomatoPHP\TomatoEddy\Http\Requests;

use TomatoPHP\TomatoEddy\Enums\Enum;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use TomatoPHP\TomatoEddy\Enums\Models\SiteType;
use TomatoPHP\TomatoEddy\Enums\Server\PhpVersion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class UpdateSiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        /** @var Server */
        $server = $this->route('server');

        /** @var Site */
        $site = $this->route('site');

        $rules = [
            'php_version' => [$site->type === SiteType::Static ? 'nullable' : 'required', Enum::rule(PhpVersion::class), Rule::in(array_keys($server->installedPhpVersions()))],
            'web_folder' => [Enum::requiredUnless(SiteType::Wordpress, 'type'), 'string', 'max:255'],
        ];

        if (! $site->repository_url) {
            return $rules;
        }

        return $rules + [
            'repository_url' => ['required', 'string', 'max:255'],
            'repository_branch' => ['required', 'string', 'max:255'],
        ];
    }
}
