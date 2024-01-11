<x-eddy-task-shell-defaults />

@if($rule->action === \TomatoPHP\TomatoEddy\Enums\Firewall\RuleAction::Allow)
    ufw {!! $rule->formatAsUfwRule() !!}
@else
    ufw insert 1 {!! $rule->formatAsUfwRule() !!}
@endif
