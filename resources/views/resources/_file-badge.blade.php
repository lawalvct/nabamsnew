@php
    $tones = [
        'pdf' => 'bg-red-600',
        'docx' => 'bg-blue-600',
        'xlsx' => 'bg-emerald-600',
        'csv' => 'bg-emerald-600',
        'pptx' => 'bg-orange-500',
        'mp3' => 'bg-purple-600',
        'mp4' => 'bg-purple-600',
        'txt' => 'bg-slate-600',
    ];
@endphp
<span class="grid {{ $size ?? 'h-12 w-12' }} shrink-0 place-items-center rounded-lg {{ $tones[$extension] ?? 'bg-[#0A2A6B]' }} text-xs font-black uppercase tracking-wide text-white">{{ $extension }}</span>
