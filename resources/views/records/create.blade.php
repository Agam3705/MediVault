<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ auth()->user()->role === 'Doctor' && isset($patient) ? route('records.patient', $patient->id) : route('dashboard') }}" class="p-2.5 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] rounded-xl transition font-extrabold flex items-center justify-center shrink-0 w-10 h-10" title="{{ __('Back') }}">
                &larr;
            </a>
            <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                <span>📄</span> Upload Medical Record
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-8 shadow-sm">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('records.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                @if(auth()->user()->role === 'Doctor')
                    @if(isset($patient))
                        <div class="mb-4 p-4 bg-[#EFEBE9]/40 border border-[#D7CCC8]/60 rounded-2xl flex items-center justify-between">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-[#8D6E63] tracking-wider">{{ __('Uploading Report For Patient') }}</p>
                                <p class="text-sm font-extrabold text-[#3E2723] mt-0.5">{{ $patient->user->name }}</p>
                                <p class="text-[11px] text-[#8D6E63]">{{ $patient->user->email }}</p>
                            </div>
                            <span class="text-xs bg-[#3E2723] text-[#FFF8E1] px-3 py-1.5 rounded-xl font-bold">
                                {{ $patient->blood_group ?? __('N/A') }}
                            </span>
                        </div>
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    @elseif(isset($activeGrants))
                        <div>
                            <x-input-label for="patient_id" :value="__('Select Authorized Patient')" />
                            <select id="patient_id" name="patient_id" required class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm py-2.5 px-3.5 font-medium transition duration-150">
                                <option value="" disabled selected>{{ __('Choose Patient...') }}</option>
                                @foreach($activeGrants as $grant)
                                    <option value="{{ $grant->patient->id }}" {{ old('patient_id') == $grant->patient->id ? 'selected' : '' }}>
                                        {{ $grant->patient->user->name }} ({{ $grant->patient->user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('patient_id')" class="mt-2" />
                        </div>
                    @endif
                @endif

                <!-- Record Type -->
                <div>
                    <x-input-label for="type" :value="__('Record Category')" />
                    <select id="type" name="type" required class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm py-2.5 px-3.5 font-medium transition duration-150">
                        <option value="Lab Report" {{ old('type') == 'Lab Report' ? 'selected' : '' }}>🩸 Lab Report</option>
                        <option value="Prescription" {{ old('type') == 'Prescription' ? 'selected' : '' }}>💊 Prescription</option>
                        <option value="Radiology" {{ old('type') == 'Radiology' ? 'selected' : '' }}>🩻 Radiology / Imaging</option>
                        <option value="Vaccination" {{ old('type') == 'Vaccination' ? 'selected' : '' }}>💉 Vaccination</option>
                        <option value="Discharge Summary" {{ old('type') == 'Discharge Summary' ? 'selected' : '' }}>🏥 Discharge Summary</option>
                        <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>📄 Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <!-- Title -->
                <div>
                    <x-input-label for="title" :value="__('Record Title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required placeholder="e.g. Blood Panel Results — May 2026" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <!-- Description -->
                <div>
                    <x-input-label for="description" :value="__('Clinical Notes / Description')" />
                    <textarea id="description" name="description" rows="4" required class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm py-2.5 px-3.5 font-medium transition duration-150" placeholder="Describe what this document is, any findings, or physician notes...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- File Upload -->
                <div>
                    <x-input-label for="file" :value="__('Attach File (PDF, JPG, PNG — max 10MB)')" />
                    <div class="mt-1 flex items-center justify-center w-full">
                        <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#D7CCC8] rounded-2xl cursor-pointer bg-[#FDFBF7] hover:bg-[#F5F2EB] transition duration-150">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <span class="text-2xl mb-2">📎</span>
                                <p class="text-xs text-[#8D6E63] font-semibold">Click to upload or drag file here</p>
                                <p class="text-[10px] text-[#BCAAA4] mt-0.5">PDF, JPG, PNG, WEBP up to 10MB</p>
                            </div>
                            <input id="file" name="file" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.webp" />
                        </label>
                    </div>
                    <p id="file-name" class="text-xs text-[#5D4037] font-semibold mt-2 hidden"></p>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <!-- Critical Toggle -->
                <div class="flex items-center gap-3 bg-red-50/50 border border-red-100 rounded-2xl p-4">
                    <input type="checkbox" id="is_critical" name="is_critical" value="1" {{ old('is_critical') ? 'checked' : '' }} class="rounded border-[#D7CCC8] text-red-600 focus:ring-red-500 w-5 h-5">
                    <div>
                        <label for="is_critical" class="text-sm font-bold text-red-700 cursor-pointer">Mark as Critical</label>
                        <p class="text-[10px] text-red-500 mt-0.5">Critical records are highlighted with a red indicator on your dashboard and are prioritized during emergency scans.</p>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-between pt-4 border-t border-[#D7CCC8]/40">
                    <a href="{{ auth()->user()->role === 'Doctor' && isset($patient) ? route('records.patient', $patient->id) : route('dashboard') }}" class="text-sm text-[#8D6E63] hover:text-[#5D4037] font-semibold transition">{{ __('Cancel') }}</a>
                    <x-primary-button>
                        {{ __('Save Medical Record') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const el = document.getElementById('file-name');
            
            if (file) {
                const name = file.name;
                const size = file.size; // in bytes
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
                
                // Size Check (10MB = 10 * 1024 * 1024 bytes)
                if (size > 10485760) {
                    alert('⚠️ Warning: File size exceeds the 10MB limit! Please compress your file or choose a smaller one.');
                    el.innerHTML = '<span class="text-red-600 font-bold">⚠️ Selected file is too large (' + (size / (1024 * 1024)).toFixed(2) + 'MB). Limit is 10MB.</span>';
                    el.classList.remove('hidden');
                    e.target.value = ''; // Clear selected file
                    return;
                }
                
                // Format Check (Fall back to extension checking if MIME type is empty)
                const extension = name.split('.').pop().toLowerCase();
                const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
                if (file.type && !allowedTypes.includes(file.type) && !allowedExtensions.includes(extension)) {
                    alert('⚠️ Warning: Unsupported file format! Please upload PDF, JPG, PNG, or WEBP only.');
                    el.innerHTML = '<span class="text-red-600 font-bold">⚠️ Unsupported file type. Please select a valid document.</span>';
                    el.classList.remove('hidden');
                    e.target.value = ''; // Clear selected file
                    return;
                }

                el.innerHTML = '<span class="text-green-700 font-bold">📎 Selected: ' + name + ' (' + (size / (1024 * 1024)).toFixed(2) + 'MB)</span>';
                el.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
