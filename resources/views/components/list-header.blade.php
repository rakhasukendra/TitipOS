@props(['eyebrow' => null, 'title', 'metrics' => []])

<div class="border-b border-hairline bg-canvas/40 px-5 py-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0">
            @if($eyebrow)
                <p class="section-label">{{ $eyebrow }}</p>
            @endif
            <h2 class="mt-1 text-base font-semibold tracking-tight text-ink-900">{{ $title }}</h2>
        </div>
        @isset($actions)
            <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>

    @if(count($metrics) > 0)
    <dl class="mt-3 flex flex-wrap items-baseline gap-x-3 gap-y-1.5">
        @foreach($metrics as $index => $metric)
            @if($index > 0)
                <span class="self-center size-1 rounded-full bg-ink-400/40" aria-hidden="true"></span>
            @endif
            <div class="flex items-baseline gap-1.5">
                <dt class="text-xs text-ink-500">{{ $metric['label'] }}</dt>
                <dd class="text-[13px] font-semibold tabular-nums text-ink-900">{{ $metric['value'] }}</dd>
            </div>
        @endforeach
    </dl>
    @endif
</div>
