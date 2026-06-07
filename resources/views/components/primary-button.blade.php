<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-brand px-5 py-2.5 text-sm']) }}>
    {{ $slot }}
</button>
