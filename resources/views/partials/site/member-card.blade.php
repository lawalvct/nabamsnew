@php
    $gradients = [
        'from-[#0A2A6B] via-[#1FA774] to-[#F5B400]',
        'from-[#F5B400] via-[#1FA774] to-[#0A2A6B]',
        'from-[#1FA774] via-[#F5B400] to-[#0A2A6B]',
        'from-[#0A2A6B] via-[#F5B400] to-[#1FA774]',
        'from-[#1FA774] via-[#0A2A6B] to-[#F5B400]',
        'from-[#F5B400] via-[#0A2A6B] to-[#1FA774]',
    ];
    $displayName = $member->nickname ?: $member->name;
    $levelText = trim(($member->academic_level ?? '').' '.($member->member_type ?? ''));
@endphp

<article class="member-card group rounded-lg bg-gradient-to-br {{ $gradients[$gradientIndex % count($gradients)] }} p-[3px] shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl" data-member-card>
    <button type="button" class="relative block w-full overflow-hidden rounded-md bg-[#0A2A6B] text-left" aria-label="Reveal member">
        <img src="{{ $member->public_image_url }}" alt="NABAMS member portrait" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105">
        <span class="absolute inset-0 bg-gradient-to-t from-[#0A2A6B]/95 via-[#0A2A6B]/20 to-transparent opacity-0 transition duration-300 group-hover:opacity-100" data-member-overlay></span>
        <span class="absolute inset-x-0 bottom-0 translate-y-full p-3 text-white transition duration-300 group-hover:translate-y-0" data-member-details>
            <span class="block truncate text-sm font-black">{{ $displayName ?: 'NABAMS Member' }}</span>
            <span class="mt-1 block truncate text-xs font-bold text-[#F5B400]">{{ $levelText ?: 'Business Administration & Management' }}</span>
        </span>
    </button>
</article>
