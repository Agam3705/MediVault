<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
            <span>⚙️</span> {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pb-12">

        <!-- Status Alert Toast -->
        @if (session('status') === 'profile-updated')
            <div class="p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-800 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">Your profile settings have been updated successfully.</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN: Demographic / Clinical / Credential Details (8 Cols) -->
            <div class="lg:col-span-8 flex flex-col gap-8">
                
                <!-- Clinical / Professional Details Card -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 md:p-8 shadow-sm">
                    <div class="border-b border-[#D7CCC8]/40 pb-4 mb-6">
                        @if($user->role === 'Patient')
                            <h3 class="text-lg font-extrabold text-[#3E2723] flex items-center gap-2">
                                <span>🩺</span> Medical & Demographics Portal
                            </h3>
                            <p class="text-xs text-[#8D6E63] mt-1">Configure your personal demographics, emergency responder information, and health status card.</p>
                        @else
                            <h3 class="text-lg font-extrabold text-[#3E2723] flex items-center gap-2">
                                <span>👨‍⚕️</span> Professional Medical Credentials
                            </h3>
                            <p class="text-xs text-[#8D6E63] mt-1">Provide your license registration, specialization category, and clinical hospital affiliation.</p>
                        @endif
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('patch')

                        <!-- Hidden fields to retain Breeze standard validators if needed -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Registered Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>
                        </div>

                        <!-- PATIENT SECTION -->
                        @if($user->role === 'Patient')
                            <div class="border-t border-[#D7CCC8]/30 pt-6 mt-6 space-y-6">
                                <h4 class="text-xs uppercase font-extrabold text-[#8D6E63] tracking-widest mb-4">Patient Demographics</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div>
                                        <x-input-label for="dob" :value="__('Date of Birth')" />
                                        <x-text-input id="dob" name="dob" type="date" class="mt-1 block w-full" :value="old('dob', $patient->dob ? $patient->dob->format('Y-m-d') : '')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('dob')" />
                                    </div>

                                    <div>
                                        <x-input-label for="gender" :value="__('Gender Identity')" />
                                        <select id="gender" name="gender" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm py-2.5 px-3.5 font-medium transition duration-150">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender', $patient->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $patient->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender', $patient->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                                    </div>

                                    <div>
                                        <x-input-label for="blood_group" :value="__('Blood Group')" />
                                        <select id="blood_group" name="blood_group" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm py-2.5 px-3.5 font-medium transition duration-150">
                                            <option value="">Select Blood Group</option>
                                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                                <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('blood_group')" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <x-input-label for="phone" :value="__('Contact Phone')" />
                                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $patient->phone)" placeholder="+1 (555) 000-0000" />
                                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                    </div>

                                    <div>
                                        <x-input-label for="profile_photo_file" :value="__('Profile Photo (Avatar)')" />
                                        <input type="file" id="profile_photo_file" name="profile_photo_file" accept="image/*" class="mt-1 block w-full text-xs text-[#5D4037] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#EFEBE9] file:text-[#5D4037] hover:file:bg-[#D7CCC8]/60 cursor-pointer" />
                                        @if($patient->profile_photo)
                                            <div class="mt-2 flex items-center gap-2">
                                                <img src="{{ $patient->profile_photo }}" class="w-10 h-10 rounded-full object-cover border border-[#D7CCC8]" alt="Avatar">
                                                <span class="text-[10px] text-[#8D6E63]">Current Photo active</span>
                                            </div>
                                        @endif
                                        <x-input-error class="mt-2" :messages="$errors->get('profile_photo_file')" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <x-input-label for="emergency_contact_name" :value="__('Emergency Contact Name')" />
                                        <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full" :value="old('emergency_contact_name', $patient->emergency_contact_name)" placeholder="Name of primary contact" />
                                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
                                    </div>

                                    <div>
                                        <x-input-label for="emergency_contact_phone" :value="__('Emergency Contact Phone')" />
                                        <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="text" class="mt-1 block w-full" :value="old('emergency_contact_phone', $patient->emergency_contact_phone)" placeholder="+1 (555) 000-0000" />
                                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_phone')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="address" :value="__('Residential Address')" />
                                    <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm py-2.5 px-3.5 font-medium transition" placeholder="Enter complete home address...">{{ old('address', $patient->address) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <!-- Emergency QR Card Subdetails -->
                                <div class="border-t border-[#D7CCC8]/30 pt-6 space-y-5 bg-[#FDFBF7] p-5 rounded-2xl border border-[#D7CCC8]/40">
                                    <h4 class="text-xs uppercase font-extrabold text-[#5D4037] tracking-wider">🎴 Emergency Responder Details</h4>
                                    <p class="text-[11px] text-[#8D6E63] -mt-2">This information will be displayed on your public emergency card when scanned by health practitioners.</p>

                                    <!-- Public Access Toggle -->
                                    <div class="flex items-center gap-3 bg-white border border-[#D7CCC8]/30 rounded-xl p-3.5">
                                        <input type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public', $emergencyCard->is_public ?? false) ? 'checked' : '' }} class="rounded border-[#D7CCC8] text-[#5D4037] focus:ring-[#5D4037] w-5 h-5">
                                        <div>
                                            <label for="is_public" class="text-xs font-bold text-[#3E2723] cursor-pointer">Enable Public QR Card Scanning</label>
                                            <p class="text-[10px] text-[#8D6E63] mt-0.5">Allows responders to view your basic emergency profile in critical conditions without auth.</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <x-input-label for="allergies" :value="__('Known Allergies')" />
                                            <textarea id="allergies" name="allergies" rows="2" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-white text-xs rounded-xl shadow-sm py-2 px-3 transition" placeholder="e.g. Penicillin, Peanuts, Latex">{{ old('allergies', $emergencyCard->allergies ?? '') }}</textarea>
                                        </div>

                                        <div>
                                            <x-input-label for="medications" :value="__('Active Medications')" />
                                            <textarea id="medications" name="medications" rows="2" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-white text-xs rounded-xl shadow-sm py-2 px-3 transition" placeholder="e.g. Lisinopril 10mg daily, Metformin 500mg">{{ old('medications', $emergencyCard->medications ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <x-input-label for="conditions" :value="__('Critical Medical Conditions')" />
                                            <textarea id="conditions" name="conditions" rows="2" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-white text-xs rounded-xl shadow-sm py-2 px-3 transition" placeholder="e.g. Type II Diabetes, Hypertension, Asthma">{{ old('conditions', $emergencyCard->conditions ?? '') }}</textarea>
                                        </div>

                                        <div>
                                            <x-input-label for="notes" :value="__('Special Rescue Notes')" />
                                            <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-white text-xs rounded-xl shadow-sm py-2 px-3 transition" placeholder="e.g. Wearing a pacemaker on left side">{{ old('notes', $emergencyCard->notes ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- DOCTOR SECTION -->
                        @if($user->role === 'Doctor')
                            <div class="border-t border-[#D7CCC8]/30 pt-6 mt-6 space-y-5">
                                <h4 class="text-xs uppercase font-extrabold text-[#8D6E63] tracking-widest mb-4">Doctor Professional Details</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <x-input-label for="license_number" :value="__('Medical License Registration No.')" />
                                        <x-text-input id="license_number" name="license_number" type="text" class="mt-1 block w-full" :value="old('license_number', $doctor->license_number)" required placeholder="e.g. MD-19283-NY" />
                                        <x-input-error class="mt-2" :messages="$errors->get('license_number')" />
                                    </div>

                                    <div>
                                        <x-input-label for="specialization" :value="__('Clinical Specialization Area')" />
                                        <x-text-input id="specialization" name="specialization" type="text" class="mt-1 block w-full" :value="old('specialization', $doctor->specialization)" required placeholder="e.g. Cardiology, Pediatrics" />
                                        <x-input-error class="mt-2" :messages="$errors->get('specialization')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="hospital" :value="__('Primary Affiliated Hospital / Clinic')" />
                                    <x-text-input id="hospital" name="hospital" type="text" class="mt-1 block w-full" :value="old('hospital', $doctor->hospital)" required placeholder="e.g. City General Hospital, Department of Cardiology" />
                                    <x-input-error class="mt-2" :messages="$errors->get('hospital')" />
                                </div>

                                <div>
                                    <x-input-label for="bio" :value="__('Practitioner Professional Biography')" />
                                    <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm py-2.5 px-3.5 font-medium transition" placeholder="Tell patients and colleagues about your medical education, experience, and areas of study...">{{ old('bio', $doctor->bio) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center gap-4 border-t border-[#D7CCC8]/40 pt-6">
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- RIGHT COLUMN: Account Security / Change Password / Account Deletion (4 Cols) -->
            <div class="lg:col-span-4 flex flex-col gap-8">
                
                <!-- Update Password Card -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                    <h3 class="text-sm font-extrabold text-[#3E2723] flex items-center gap-1.5 mb-2">
                        <span>🔑</span> Change Password
                    </h3>
                    <p class="text-xs text-[#8D6E63] mb-4">Ensure your account is using a long, random password to stay secure.</p>

                    @include('profile.partials.update-password-form')
                </div>

                <!-- Notification Preferences Card -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                    <h3 class="text-sm font-extrabold text-[#3E2723] flex items-center gap-1.5 mb-2">
                        <span>🔔</span> Email Alerts Preference
                    </h3>
                    <p class="text-xs text-[#8D6E63] mb-4">Toggle which system-wide health and security events trigger automated email notifications to your inbox.</p>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')
                        
                        <!-- Keep name and email values so the profile validator is satisfied -->
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">

                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="notify_on_access_request" name="notify_on_access_request" value="1" {{ $user->getSetting('notify_on_access_request', true) ? 'checked' : '' }} class="rounded border-[#D7CCC8] text-[#5D4037] focus:ring-[#5D4037] mt-0.5 w-4 h-4">
                                <label for="notify_on_access_request" class="text-xs font-semibold text-[#3E2723] cursor-pointer">
                                    Incoming access request requests
                                    <p class="text-[10px] text-[#8D6E63] font-normal">Get notified when a clinical practitioner requests view permissions to your records.</p>
                                </label>
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="notify_on_record_viewed" name="notify_on_record_viewed" value="1" {{ $user->getSetting('notify_on_record_viewed', true) ? 'checked' : '' }} class="rounded border-[#D7CCC8] text-[#5D4037] focus:ring-[#5D4037] mt-0.5 w-4 h-4">
                                <label for="notify_on_record_viewed" class="text-xs font-semibold text-[#3E2723] cursor-pointer">
                                    Records index view audit alerts
                                    <p class="text-[10px] text-[#8D6E63] font-normal">Get notified whenever your clinical record list is viewed by doctors.</p>
                                </label>
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="notify_on_record_uploaded" name="notify_on_record_uploaded" value="1" {{ $user->getSetting('notify_on_record_uploaded', true) ? 'checked' : '' }} class="rounded border-[#D7CCC8] text-[#5D4037] focus:ring-[#5D4037] mt-0.5 w-4 h-4">
                                <label for="notify_on_record_uploaded" class="text-xs font-semibold text-[#3E2723] cursor-pointer">
                                    New medical document upload
                                    <p class="text-[10px] text-[#8D6E63] font-normal">Receive an email when new files or diagnostics are uploaded to your profile.</p>
                                </label>
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="notify_on_qr_scan" name="notify_on_qr_scan" value="1" {{ $user->getSetting('notify_on_qr_scan', true) ? 'checked' : '' }} class="rounded border-[#D7CCC8] text-[#5D4037] focus:ring-[#5D4037] mt-0.5 w-4 h-4">
                                <label for="notify_on_qr_scan" class="text-xs font-semibold text-[#3E2723] cursor-pointer">
                                    Emergency QR card scan alerts
                                    <p class="text-[10px] text-[#8D6E63] font-normal">Get instantly emailed when emergency responders scan your public QR code.</p>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 border-t border-[#D7CCC8]/40 pt-4 mt-2">
                            <x-primary-button>{{ __('Save Alert Options') }}</x-primary-button>
                        </div>
                    </form>
                </div>

                <!-- Account Deletion -->
                <div class="bg-red-50/50 rounded-3xl border border-red-200/50 p-6 shadow-sm">
                    <h3 class="text-sm font-extrabold text-red-700 flex items-center gap-1.5 mb-2">
                        <span>⚠️</span> Delete Account
                    </h3>
                    <p class="text-xs text-red-500/95 mb-4">Once your account is deleted, all medical records, consent authorizations, and audit logs will be permanently erased.</p>

                    @include('profile.partials.delete-user-form')
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
