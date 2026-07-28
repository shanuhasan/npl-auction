@props(['id', 'options' => [], 'placeholder' => 'Select an option'])

<div wire:ignore x-data="{
    init() {
        let el = $(this.$refs.select);
        
        el.select2({
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            width: '100%'
        });

        @php $model = $attributes->whereStartsWith('wire:model')->first(); @endphp
        
        @if($model)
            // Set initial value
            let initialValue = $wire.get('{{ $model }}');
            if (initialValue !== undefined && initialValue !== null) {
                el.val(initialValue).trigger('change.select2');
            }
            
            // Sync from Livewire to Select2
            $watch('$wire.{{ $model }}', (value) => {
                if (el.val() !== value) {
                    el.val(value).trigger('change.select2');
                }
            });

            // Sync from Select2 to Livewire
            el.on('change', (e) => {
                let currentVal = el.val();
                if ($wire.get('{{ $model }}') !== currentVal) {
                    $wire.set('{{ $model }}', currentVal);
                }
            });
        @endif
    }
}">
    <select x-ref="select" id="{{ $id }}" {{ $attributes->merge(['class' => 'select2 w-full']) }}>
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
        
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
