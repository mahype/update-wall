@props(['status'])

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $status->badgeClasses() }}">
    <span class="w-2 h-2 rounded-full {{ $status->dotClasses() }}"></span>
    {{ $status->label() }}
</span>
