<x-splade-modal>
    <h1 class="text-2xl font-bold pb-4 border-b">{{__('Edit Account ID:') . $account->id }}  </h1>
    <x-splade-form :default="$account->toArray()" class="flex flex-col gap-4 mt-4" :action="route('servers.sites.accounts.update', [
        'site'=>$site,
        'server'=>$server,
        'account'=>$account
    ])" method="POST">
        <x-splade-input :label="__('Email')" type="email" name="email" />
        <x-splade-input :label="__('Password')" type="text" name="password" />
        <x-splade-input :label="__('IP')" type="text" name="ip" />
        <x-splade-input :label="__('Sub IP')" type="text" name="sub_ip" />
        <x-splade-input :label="__('Recovery Email')" type="email" name="recovery_email" />
        <x-splade-select choices :options="[
                'socks5' => 'socks5',
                'socks4' => 'socks4',
                'https' => 'https',
            ]" :label="__('Proxy Type')" name="proxy_type" />
        <div class="flex justify-start gap-4">
            <x-splade-submit />
            <x-splade-form confirm method="DELETE" :action="route('servers.sites.accounts.destroy', [
                    'site'=>$site,
                    'server'=>$server,
                    'account'=>$account
                ])">
                <x-splade-submit danger label="Delete Account" />
            </x-splade-form>
        </div>
    </x-splade-form>

</x-splade-modal>
