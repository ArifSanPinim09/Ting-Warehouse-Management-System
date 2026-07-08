@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent/40 rounded-input shadow-sm']) }}>
