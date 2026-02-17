@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-input bg-background text-foreground focus:border-ring focus:ring-ring rounded-md shadow-sm']) }}>
