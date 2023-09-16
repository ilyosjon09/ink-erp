<x-dynamic-component :component="$getFieldWrapperView()" :id="$getId()" :label="$getLabel()"
    :label-sr-only="$isLabelHidden()" :helper-text="$getHelperText()" :hint="$getHint()" :hint-action="$getHintAction()"
    :hint-color="$getHintColor()" :hint-icon="$getHintIcon()" :required="$isRequired()" :state-path="$getStatePath()">
    <img src="{{ asset('storage/' . $getState()) }}" />
</x-dynamic-component>