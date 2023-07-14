<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        <input
            type="button"
            value="Add question alternative (from highlight)"
            wire:loading.class.delay="opacity-70 cursor-wait"
            wire:click="redirect_back('{{$getId()}}')"
            class="filament-button filament-button-size-md inline-flex
            items-center justify-center py-1 gap-1 font-medium
            rounded-lg border transition-colors outline-none
            focus:ring-offset-2 focus:ring-2 focus:ring-inset
             dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm
              text-gray-800 bg-white border-gray-300
              hover:bg-gray-50 focus:ring-primary-600
               focus:text-primary-600 focus:bg-primary-50
                focus:border-primary-600 dark:bg-gray-800
                dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-page-button-action">
    </div>
</x-dynamic-component>
