<?php

namespace TomatoPHP\TomatoEddy\Services;

use TomatoPHP\TomatoEddy\Models\Credentials;
use Cloudflare\Api;
use Cloudflare\Zone\Dns;

class Cloudflare
{
    protected $cloudflare;

    public function __construct()
    {
        $cloudflareKey = Credentials::where('provider', 'cloudflare')->first();
        if (! empty($cloudflareKey->credentials)) {
            $this->cloudflare = new Api($cloudflareKey->credentials['cloudflare_email'], $cloudflareKey->credentials['cloudflare_key']);
        }
    }

    public function create($domain, $ip)
    {
        $dns = new Dns($this->cloudflare);
        $cloudflareKey = Credentials::where('provider', 'cloudflare')->first();
        $cloudflareID = $dns->create($cloudflareKey->credentials['cloudflare_domain'], 'A', $domain, $ip, 120);
        if ($cloudflareID->success) {
            return $cloudflareID->result->id;
        } else {
            return $cloudflareID->errors;
        }
    }

    public function delete($id)
    {
        $dns = new Dns($this->cloudflare);
        $cloudFlareId = $dns->delete_record($this->settings->domain_dns_zone, $id);

        if ($cloudFlareId->success) {
            return true;
        } else {
            return $cloudFlareId->errors;
        }
    }
}
