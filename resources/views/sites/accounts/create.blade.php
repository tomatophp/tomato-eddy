<x-splade-modal>
    <h1 class="text-2xl font-bold pb-4 border-b">{{__('Create New Account')}}</h1>
    <x-splade-data :default="['tab' => 'create']">
        <div class="flex justify-between gap-4 mt-4">
            <div class="w-full">
                <x-splade-button v-if="data.tab === 'import'" @click="data.tab = 'create'" secondary :label="__('Create Account')" class="w-full" />
                <x-splade-button v-if="data.tab === 'create'" @click="data.tab = 'create'" :label="__('Create Account')" class="w-full" />
            </div>
            <div class="w-full">
                <x-splade-button v-if="data.tab === 'create'" @click="data.tab = 'import'" secondary :label="__('Import From TXT')" class="w-full"/>
                <x-splade-button v-if="data.tab === 'import'" @click="data.tab === 'import'" @click="data.tab ='import'" :label="__('Import From TXT')" class="w-full"/>
            </div>
        </div>
        <x-splade-form v-if="data.tab==='create'" :default="['proxy_type' => 'socks5']" class="flex flex-col gap-4 mt-4" :action="route('servers.sites.accounts.store', ['site'=>$site, 'server'=>$server])" method="POST">
            <x-splade-input :label="__('Email')" type="email" name="email" />
            <x-splade-input :label="__('Password')" type="password" name="password" />
            <x-splade-input :label="__('IP')" type="text" name="ip" />
            <x-splade-input :label="__('Sub IP')" type="text" name="sub_ip" />
            <x-splade-input :label="__('Recovery Email')" type="email" name="recovery_email" />
            <x-splade-select choices :options="[
                'socks5' => 'socks5',
                'socks4' => 'socks4',
                'https' => 'https',
            ]" :label="__('Proxy Type')" name="proxy_type" />
            <x-splade-submit />
        </x-splade-form>
        <x-splade-form class="flex flex-col gap-4 mt-4" v-if="data.tab==='import'" :default="['proxy_type' => 'socks5']" :action="route('servers.sites.accounts.import', ['site'=>$site, 'server'=>$server])" method="POST">
            <x-splade-file  :label="__('TXT File')" name="txt" />
            <x-splade-submit />
        </x-splade-form>
    </x-splade-data>
</x-splade-modal>
