@props(['id', 'options' => [], 'placeholder' => 'Select an option'])

<div wire:ignore x-data="{
    init() {
        let el = $(this.$refs.select);
        
        el.select2({
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            width: '100%'
        });

        el.on('change', (e) => {
            let modelName = '{{ $attributes->whereStartsWith('wire:model')->first() }}';
            if (modelName) {
                @this.set(modelName, el.val());
            }
        });
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
