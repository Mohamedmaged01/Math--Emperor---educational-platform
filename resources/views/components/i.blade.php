@props(['name'])
{{ svg('lucide-'.$name, trim($attributes->get('class', '')), $attributes->except('class')->merge(['aria-hidden' => 'true'])->getAttributes()) }}
