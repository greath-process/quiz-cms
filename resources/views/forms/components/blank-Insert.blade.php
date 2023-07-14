@php
$text_blank = "{{BLANK}}";
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :id="$getId()" :label="$getLabel()" :label-sr-only="$isLabelHidden()" :helper-text="$getHelperText()" :hint="$getHint()" :hint-action="$getHintAction()" :hint-color="$getHintColor()" :hint-icon="$getHintIcon()" :required="$isRequired()" :state-path="$getStatePath()">
    <div class="relative overflow-hidden" x-data="{characterLimit: {{ $getCharacterLimit() }}, characterCount: {{ mb_strlen($getState() ? $getState() : '') }}}">
        <div class="bg-white dark:bg-gray-700 absolute right-1 rtl:!left-1 rtl:!right-auto px-2 bottom-1 pb-1 text-sm" @if($getCharacterLimit()) :class="{'text-danger-500': characterCount > {{ $getCharacterLimit() }}}" @endif>
            <span x-text="characterCount"></span>@if($getCharacterLimit())/{{ $getCharacterLimit() }}@endif
        </div>
        <button id="my-button" class="filament-forms-markdown-editor-component-toolbar-button h-full mb-3 cursor-pointer rounded-lg border border-gray-300 bg-white px-1.5 py-1 text-xs font-medium text-gray-800 shadow-sm transition duration-200 hover:bg-gray-100 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono" type="button" onclick="addToTextArea('{{$text_blank}}', '{{$getId()}}')">{{$text_blank}}</button>
        <br>
        <textarea id="{{$getId()}}" @keyup="characterCount = $event.target.value.length" {!! ($autocapitalize=$getAutocapitalize()) ? "autocapitalize=\" {$autocapitalize}\"" : null !!} {!! ($autocomplete=$getAutocomplete()) ? "autocomplete=\" {$autocomplete}\"" : null !!} {!! $isAutofocused() ? 'autofocus' : null !!} {!! ($cols=$getCols()) ? "cols=\" {$cols}\"" : null !!} {!! $isDisabled() ? 'disabled' : null !!}  dusk="filament.forms.{{ $getStatePath() }}" {!! ($placeholder=$getPlaceholder()) ? "placeholder=\" {$placeholder}\"" : null !!} {!! ($rows=$getRows()) ? "rows=\" {$rows}\"" : null !!} {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" @if (! $isConcealed()) {!! filled($length=$getMaxLength()) ? "maxlength=\" {$length}\"" : null !!} {!! filled($length=$getMinLength()) ? "minlength=\" {$length}\"" : null !!} {!! $isRequired() ? 'required' : null !!} @endif {{
            $attributes
                ->merge($getExtraAttributes())
                ->merge($getExtraInputAttributeBag()->getAttributes())
                ->class([
                    'filament-forms-textarea-component block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70',
                    'dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-primary-500' => config('forms.dark_mode'),
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                    'dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()) && config('forms.dark_mode')
                ])
        }} @if ($shouldAutosize()) x-data="textareaFormComponent()" x-on:input="render()" style="height: 150px" {{ $getExtraAlpineAttributeBag() }} @endif></textarea>
    </div>
    <script>
        const addToTextArea = function(text_to_add, textarea_id) {
            let textarea = document.getElementById(textarea_id);
            let start_position = textarea.selectionStart;
            let end_position = textarea.selectionEnd;

            if (start_position > 0 && !/\s/.test(textarea.value.charAt(start_position - 1))) {
                text_to_add = " " + text_to_add;
            }

            if (end_position < textarea.value.length && !/\s/.test(textarea.value.charAt(end_position))) {
                text_to_add += " ";
            }

            textarea.value = `${textarea.value.substring(
    0,
    start_position
  )}${text_to_add}${textarea.value.substring(
    end_position,
    textarea.value.length
  )}`;
        };
    </script>
</x-dynamic-component>
