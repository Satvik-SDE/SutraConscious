@php
    $steps = $order->fulfillmentTimeline();
@endphp

<ol class="space-y-0">
    @foreach($steps as $index => $step)
        <li class="relative flex gap-4 pb-8 last:pb-0">
            @if(! $loop->last)
                <span class="absolute left-[11px] top-6 bottom-0 w-px {{ $step['done'] ? 'bg-brand-blue/40' : 'bg-surface-line' }}" aria-hidden="true"></span>
            @endif
            <span class="relative z-10 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2
                {{ $step['current'] ? 'border-brand-blue bg-brand-blue text-surface-cream' : ($step['done'] ? 'border-brand-blue bg-surface-cream text-brand-blue' : 'border-surface-line bg-surface-cream text-brand-black/30') }}">
                @if($step['done'] && ! $step['current'])
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                @else
                    <span class="h-1.5 w-1.5 rounded-full {{ $step['current'] ? 'bg-surface-cream' : 'bg-current' }}"></span>
                @endif
            </span>
            <div class="min-w-0 pt-0.5">
                <p class="text-sm font-medium {{ $step['done'] || $step['current'] ? 'text-brand-black' : 'text-brand-black/45' }}">{{ $step['label'] }}</p>
                @if($step['at'])
                    <p class="mt-0.5 text-xs text-brand-black/50">{{ $step['at']->timezone(config('app.timezone'))->format('j M Y, g:i A') }}</p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
