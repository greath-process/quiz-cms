<x-filament::page
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-create-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])"
>
    <x-filament::form wire:submit.prevent="create">
        {{ $this->form }}

        <x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament::form>

    <script>
        function setTab(num){
            @this.set('tab', num);
        }

        function getTab(){
            tabs = document.querySelectorAll('[role="tab"]')
            let ind = parseInt(@this.get('tab')) - 1;
            setTimeout(function() {
                tabs[ind].click();
            }, 350);
        }

        document.addEventListener("DOMContentLoaded", function(event) {
            let tabs = document.querySelectorAll('[role="tab"]');
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => setTab(index + 1));
            })

            let sbm_btn = document.querySelector('[dusk="filament.admin.action.create"]');
            sbm_btn.addEventListener('click', () => getTab());
        });
    </script>
</x-filament::page>
