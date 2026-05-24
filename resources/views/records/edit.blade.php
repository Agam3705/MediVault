<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ auth()->user()->role === 'Doctor' ? route('records.patient', $record->patient_id) : route('dashboard') }}" class="p-2.5 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] rounded-xl transition font-extrabold flex items-center justify-center shrink-0 w-10 h-10" title="{{ __('Back') }}">
                &larr;
            </a>
            <h2 class="font-bold text-xl text-[#3E2723] leading-tight">
                ✏️ Edit Medical Record: {{ $record->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-[#D7CCC8]/60 overflow-hidden shadow-xl sm:rounded-3xl p-8">
                
                <form method="POST" action="{{ route('records.update', $record->id) }}" class="flex flex-col gap-6">
                    @csrf
                    @method('PATCH')

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">Record Category</label>
                        <select name="type" id="type" required 
                                class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition">
                            <option value="Lab Report" @selected($record->type === 'Lab Report')>Lab Report (Blood test, Path lab, etc.)</option>
                            <option value="Prescription" @selected($record->type === 'Prescription')>Prescription (Doctor prescriptions)</option>
                            <option value="Radiology" @selected($record->type === 'Radiology')>Radiology (X-Ray, MRI, CT scan)</option>
                            <option value="Vaccination" @selected($record->type === 'Vaccination')>Vaccination Certificate</option>
                            <option value="Discharge Summary" @selected($record->type === 'Discharge Summary')>Discharge Summary</option>
                            <option value="Other" @selected($record->type === 'Other')>Other Report</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">Record Title</label>
                        <input type="text" name="title" id="title" required value="{{ old('title', $record->title) }}" placeholder="e.g. Annual Health Checkup"
                               class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">Detailed Notes / Diagnosis Summary</label>
                        <textarea name="description" id="description" rows="5" required placeholder="Write clinical insights, doctor's suggestions, or comments here..."
                                  class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition">{{ old('description', $record->description) }}</textarea>
                    </div>

                    <!-- Critical Flag -->
                    <div class="flex items-center gap-3 bg-red-50/50 border border-red-100 p-4 rounded-2xl">
                        <input type="checkbox" name="is_critical" id="is_critical" value="1" @checked($record->is_critical)
                               class="w-5 h-5 border-red-200 text-red-700 focus:ring-red-600 rounded-md transition cursor-pointer">
                        <div>
                            <label for="is_critical" class="text-xs font-bold text-red-900 cursor-pointer">Mark as Critical Record</label>
                            <p class="text-[10px] text-red-700/80 mt-0.5">Critical records are flagged and highlighted immediately on clinical lookup pages.</p>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 justify-end border-t border-[#D7CCC8]/30 pt-6 mt-2">
                        <a href="{{ auth()->user()->role === 'Doctor' ? route('records.patient', $record->patient_id) : route('dashboard') }}" class="px-5 py-3 bg-[#EFEBE9] hover:bg-[#D7CCC8]/60 text-xs font-bold text-[#5D4037] rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition active:scale-95 shadow-md">
                            Save Changes & Snapshot Version
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
