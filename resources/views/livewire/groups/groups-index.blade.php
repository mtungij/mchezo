<div x-data="{ copied: null }" class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto mt-10">
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-2xl p-5 sm:p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                My Groups
            </h2>

            @if($groups->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-center">
                    You haven't created any groups yet.
                </p>
            @else
                <!-- GRID -->
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    @foreach($groups as $group)
                        <li
                            class="flex flex-col justify-between
                                   border border-gray-200 dark:border-gray-700
                                   rounded-xl p-4 sm:p-5
                                   bg-gray-50 dark:bg-gray-800
                                   hover:shadow-md transition">

                            <!-- Content -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $group->name }}
                                </h3>

                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Owner: {{ $group->owner->name ?? '—' }}
                                </p>

                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Phone: {{ $group->owner->phone ?? '—' }}
                                </p>
                            </div>

                            <!-- View Members -->
                            <a href="{{ route('groups.members', $group->id) }}"
                               class="mt-4 inline-flex justify-center items-center
                                      px-4 py-2 rounded-lg text-sm font-medium
                                      bg-blue-600 hover:bg-blue-700 text-white transition">
                                View Members
                            </a>

                            <!-- Actions -->
                            @if($group->owner_id === auth()->id())
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Copy -->
                                    <button
                                        @click="
                                            navigator.clipboard.writeText('{{ url('/invite/'.$group->invite_code) }}');
                                            copied = {{ $group->id }};
                                            setTimeout(() => copied = null, 2000)
                                        "
                                        :class="copied === {{ $group->id }}
                                            ? 'bg-green-500 hover:bg-green-600'
                                            : 'bg-cyan-500 hover:bg-cyan-600'"
                                        class="w-full py-2.5 rounded-xl text-white
                                               text-sm font-semibold transition shadow">
                                        <span x-show="copied !== {{ $group->id }}">Copy Link</span>
                                        <span x-show="copied === {{ $group->id }}">Copied!</span>
                                    </button>

                                    <!-- WhatsApp -->
                                  <a href="https://wa.me/?text={{ urlencode('Jiunge na kikundi changu: ' . url('/invite/'.$group->invite_code)) }}"
   target="_blank"
   class="w-full py-2.5 px-4 rounded-xl
          bg-gradient-to-r from-green-500 via-emerald-500 to-green-600
          hover:from-green-600 hover:to-emerald-600
          text-white text-sm font-semibold
          transition-all duration-200
          shadow-md hover:shadow-lg
          flex justify-center items-center gap-2">

    <!-- WhatsApp Icon -->
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
        <path
            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967
            -.273-.099-.471-.148-.67.15-.197.297-.767.966
            -.94 1.164-.173.199-.347.223-.644.075-.297
            -.15-1.255-.463-2.39-1.475-.883-.788-1.48
            -1.761-1.653-2.059-.173-.297-.018-.458.13
            -.606.134-.133.298-.347.446-.52.149-.174
            .198-.298.298-.497.099-.198.05-.371-.025
            -.52-.075-.149-.669-1.612-.916-2.207-.242
            -.579-.487-.5-.669-.51-.173-.008-.371-.01
            -.57-.01-.198 0-.52.074-.792.372-.272.297
            -1.04 1.016-1.04 2.479 0 1.462 1.065 2.875
            1.213 3.074.149.198 2.096 3.2 5.077 4.487
            .709.306 1.262.489 1.694.625.712.227 1.36
            .195 1.871.118.571-.085 1.758-.719 2.006
            -1.413.248-.694.248-1.289.173-1.413
            -.074-.124-.272-.198-.57-.347z"/>
    </svg>

    <span>Share</span>
</a>

                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
