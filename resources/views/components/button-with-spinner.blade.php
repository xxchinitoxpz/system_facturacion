@props([
    'type' => 'submit',
    'loadingText' => 'Procesando...',
    'defaultText' => 'Enviar',
    'spinnerId' => 'spinner',
    'textId' => 'text'
])

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => 'px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2']) }}
>
    <span id="{{ $textId }}">{{ $defaultText }}</span>
    <div id="{{ $spinnerId }}" class="hidden">
        <x-spinner />
    </div>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const button = document.querySelector('button[type="{{ $type }}"]');
    const text = document.getElementById('{{ $textId }}');
    const spinner = document.getElementById('{{ $spinnerId }}');
    
    if (button && text && spinner) {
        button.addEventListener('click', function() {
            if (button.type === 'submit') {
                // Deshabilitar el botón y mostrar spinner
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                text.textContent = '{{ $loadingText }}';
                spinner.classList.remove('hidden');
            }
        });
    }
});
</script> 