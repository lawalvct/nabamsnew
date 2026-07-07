@extends('layouts.dashboard', ['pageTitle' => 'Settings'])

@section('content')
    <section class="rounded-lg bg-[#0A2A6B] p-6 text-white shadow-xl sm:p-8">
        <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">System Settings</p>
        <h1 class="mt-3 text-3xl font-black sm:text-4xl">Control NABAMS features</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-[#F2F2F2]/80">
            Enable or disable public-facing features without deleting positions, aspirants, vote records, or member accounts.
        </p>
    </section>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 px-5 py-4 text-sm font-bold text-[#0A2A6B]">{{ session('success') }}</div>
    @endif

    <section class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-7">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Registration</p>
                                <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Member registration switch</h2>
                                <p class="mt-3 text-sm leading-7 text-[#2E2E2E]/70">
                                    When disabled, visitors cannot create their own accounts. Admins can still add members from the members page.
                                </p>
                            </div>
                            <span class="rounded-lg px-3 py-2 text-xs font-black {{ $registrationSetting->value === 'On' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">
                                {{ $registrationSetting->value }}
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 p-4 text-sm font-bold text-[#0A2A6B]">
                                <input type="radio" name="registration" value="On" @checked(old('registration', $registrationSetting->value) === 'On') class="mt-1 h-4 w-4 border-[#0A2A6B]/20 text-[#1FA774]">
                                <span>
                                    Enable Registration
                                    <span class="mt-1 block font-normal leading-6 text-[#2E2E2E]/70">Visitors can create member accounts.</span>
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 p-4 text-sm font-bold text-[#0A2A6B]">
                                <input type="radio" name="registration" value="Off" @checked(old('registration', $registrationSetting->value) === 'Off') class="mt-1 h-4 w-4 border-[#0A2A6B]/20 text-[#F5B400]">
                                <span>
                                    Disable Registration
                                    <span class="mt-1 block font-normal leading-6 text-[#2E2E2E]/70">Public account creation is closed until enabled again.</span>
                                </span>
                            </label>
                        </div>

                        @error('registration')
                            <span class="mt-3 block text-xs font-black text-[#F5B400]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="border-t border-[#0A2A6B]/10 pt-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Election</p>
                                <h2 class="mt-2 text-2xl font-black text-[#0A2A6B]">Election voting switch</h2>
                                <p class="mt-3 text-sm leading-7 text-[#2E2E2E]/70">
                                    When disabled, members can still see the election page message, but they cannot view aspirants or submit votes.
                                </p>
                            </div>
                            <span class="rounded-lg px-3 py-2 text-xs font-black {{ $electionSetting->value === 'On' ? 'bg-[#1FA774]/10 text-[#1FA774]' : 'bg-[#F5B400]/20 text-[#0A2A6B]' }}">
                                {{ $electionSetting->value }}
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#1FA774]/20 bg-[#1FA774]/10 p-4 text-sm font-bold text-[#0A2A6B]">
                                <input type="radio" name="election" value="On" @checked(old('election', $electionSetting->value) === 'On') class="mt-1 h-4 w-4 border-[#0A2A6B]/20 text-[#1FA774]">
                                <span>
                                    Enable Election
                                    <span class="mt-1 block font-normal leading-6 text-[#2E2E2E]/70">Members can view aspirants and submit votes.</span>
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#F5B400]/40 bg-[#F5B400]/15 p-4 text-sm font-bold text-[#0A2A6B]">
                                <input type="radio" name="election" value="Off" @checked(old('election', $electionSetting->value) === 'Off') class="mt-1 h-4 w-4 border-[#0A2A6B]/20 text-[#F5B400]">
                                <span>
                                    Disable Election
                                    <span class="mt-1 block font-normal leading-6 text-[#2E2E2E]/70">Members cannot vote until election is enabled again.</span>
                                </span>
                            </label>
                        </div>

                        @error('election')
                            <span class="mt-3 block text-xs font-black text-[#F5B400]">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="mt-6 rounded-lg bg-[#1FA774] px-6 py-3 text-sm font-black text-white transition hover:bg-[#198b61]">Save Settings</button>
            </form>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-[#0A2A6B]/10 sm:p-8">
            <p class="text-sm font-black uppercase tracking-wide text-[#F5B400]">Current Database Values</p>

            @foreach ([$registrationSetting, $electionSetting] as $setting)
                <dl class="mt-5 grid gap-4 text-sm">
                    <div class="rounded-lg bg-[#F2F2F2] p-4">
                        <dt class="font-bold text-[#2E2E2E]/60">Name</dt>
                        <dd class="mt-1 font-black text-[#0A2A6B]">{{ $setting->name }}</dd>
                    </div>
                    <div class="rounded-lg bg-[#F2F2F2] p-4">
                        <dt class="font-bold text-[#2E2E2E]/60">Slug</dt>
                        <dd class="mt-1 font-black text-[#0A2A6B]">{{ $setting->slug }}</dd>
                    </div>
                    <div class="rounded-lg bg-[#F2F2F2] p-4">
                        <dt class="font-bold text-[#2E2E2E]/60">Value</dt>
                        <dd class="mt-1 font-black text-[#0A2A6B]">{{ $setting->value }}</dd>
                    </div>
                    <div class="rounded-lg bg-[#F2F2F2] p-4">
                        <dt class="font-bold text-[#2E2E2E]/60">Active</dt>
                        <dd class="mt-1 font-black text-[#0A2A6B]">{{ $setting->active }}</dd>
                    </div>
                </dl>
            @endforeach
        </div>
    </section>
@endsection
