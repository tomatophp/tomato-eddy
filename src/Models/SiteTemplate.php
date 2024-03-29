<?php

namespace TomatoPHP\TomatoEddy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $type
 * @property boolean $zero_downtime_deployment
 * @property string $repository_url
 * @property string $repository_branch
 * @property string $web_folder
 * @property string $domain
 * @property string $php_version
 * @property boolean $has_server
 * @property string $server_name
 * @property string $server_credentials_id
 * @property string $server_custom_server
 * @property string $server_ssh_keys
 * @property string $server_type
 * @property string $server_region
 * @property string $server_image
 * @property string $hook_before_updating_repository
 * @property string $hook_after_updating_repository
 * @property string $hook_before_making_current
 * @property string $hook_after_making_current
 * @property boolean $add_server_ssh_key_to_github
 * @property boolean $add_dns_zone_to_cloudflare
 * @property boolean $has_queue
 * @property string $queue_command
 * @property boolean $has_schedule
 * @property string $schedule_command
 * @property boolean $has_database
 * @property string $database_name
 * @property string $database_user
 * @property string $database_password
 * @property string $created_at
 * @property string $updated_at
 */
class SiteTemplate extends Model
{
    use HasUlids;

    /**
     * @var array
     */
    protected $fillable = [
        'queue_command',
        'schedule_command',
        'domain',
        'has_server',
        'server_name',
        'server_credentials_id',
        'server_custom_server',
        'server_ssh_keys',
        'server_type',
        'server_region',
        'server_image',
        'name', 'type', 'zero_downtime_deployment', 'repository_url', 'repository_branch', 'web_folder', 'php_version', 'hook_before_updating_repository', 'hook_after_updating_repository', 'hook_before_making_current', 'hook_after_making_current', 'add_server_ssh_key_to_github', 'add_dns_zone_to_cloudflare', 'has_queue', 'has_schedule', 'has_database', 'database_name', 'database_user', 'database_password', 'created_at', 'updated_at'];

    protected $casts = [
        'zero_downtime_deployment' => 'boolean',
        'add_server_ssh_key_to_github' => 'boolean',
        'add_dns_zone_to_cloudflare' => 'boolean',
        'has_queue' => 'boolean',
        'has_schedule' => 'boolean',
        'has_database' => 'boolean',
        'has_server' => 'boolean',
    ];

}
