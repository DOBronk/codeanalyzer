<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('job.createtitle') }}
        </h2>
    </x-slot>

    <x-page-container class="max-w-lg">
        <p class="mb-4">{{ __('job.supplygit') }}</p>
        <x-message :message="Session('error')" error="1"/>
        <form action="{{ route('codeanalyzer.create.step.one.post') }}" method="POST" class="space-y-6">
            @csrf
            <x-text-input-xl :header="__('job.owner')" name="owner" :messages="$errors->get('owner')"/>
            <x-text-input-xl :header="__('job.repository')" name="repository" :messages="$errors->get('repository')"/>
            <div>
                <x-text-input-xl :header="__('job.branch')" name="branch" :messages="$errors->get('branch')"/>
                <p class="text-sm text-gray-500 mt-1">({{ __('job.optional') }})</p>
            </div>
            <x-button-blue>{{ __('job.next') }}</x-button-blue>
        </form>
    </x-page-container>
</x-app-layout>
