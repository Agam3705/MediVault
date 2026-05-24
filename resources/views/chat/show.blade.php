<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2.5 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] rounded-xl transition font-extrabold flex items-center justify-center shrink-0 w-10 h-10" title="{{ __('Back') }}">
                &larr;
            </a>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#EFEBE9] border border-[#D7CCC8]/80 flex items-center justify-center font-extrabold text-[#5D4037]">
                    {{ substr($targetUser->name, 0, 2) }}
                </div>
                <div>
                    <h2 class="font-extrabold text-lg text-[#3E2723] leading-tight">
                        {{ $targetUser->name }}
                    </h2>
                    <p class="text-xs text-[#8D6E63] flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        {{ $targetUser->role === 'Doctor' ? ($targetUser->doctor->specialization ?? __('Practitioner')) : __('Patient') }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 overflow-hidden shadow-sm flex flex-col h-[600px]"
             x-data="{
                messages: [],
                newMessage: '',
                isLoading: true,
                isSending: false,
                lastId: null,
                attachedFile: null,
                attachedFileName: '',

                init() {
                    this.fetchMessages();
                    // Start 3-second polling
                    setInterval(() => this.fetchMessages(), 3000);
                },

                async fetchMessages() {
                    try {
                        const res = await fetch('{{ route('chat.api.messages', $targetUser->id) }}');
                        const data = await res.json();
                        
                        // Check if message length changed before resetting and scrolling
                        if (data.length !== this.messages.length) {
                            this.messages = data;
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (e) {
                        console.error('Failed to load messages:', e);
                    } finally {
                        this.isLoading = false;
                    }
                },

                triggerFileSelect() {
                    this.$refs.fileInput.click();
                },

                handleFileChange(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.attachedFile = file;
                        this.attachedFileName = file.name;
                    }
                },

                clearAttachment() {
                    this.attachedFile = null;
                    this.attachedFileName = '';
                    this.$refs.fileInput.value = '';
                },

                async sendMessage() {
                    if (this.newMessage.trim().length === 0 && !this.attachedFile) return;
                    if (this.isSending) return;
                    this.isSending = true;

                    const formData = new FormData();
                    if (this.newMessage.trim().length > 0) {
                        formData.append('message', this.newMessage);
                    }
                    if (this.attachedFile) {
                        formData.append('file', this.attachedFile);
                    }

                    const originalText = this.newMessage;
                    this.newMessage = '';
                    this.clearAttachment();

                    try {
                        const res = await fetch('{{ route('chat.api.send', $targetUser->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                            },
                            body: formData
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.messages.push(data.message);
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (e) {
                        console.error('Failed to send message:', e);
                        this.newMessage = originalText; // Restore text
                    } finally {
                        this.isSending = false;
                    }
                },

                scrollToBottom() {
                    const el = this.$refs.chatContainer;
                    if (el) {
                        el.scrollTop = el.scrollHeight;
                    }
                }
             }">

            <!-- Messages Stream container -->
            <div x-ref="chatContainer" class="flex-grow overflow-y-auto p-6 flex flex-col gap-4 bg-[#FDFBF7]/30 border-b border-[#D7CCC8]/40">
                
                <!-- Loading State -->
                <template x-if="isLoading && messages.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-[#8D6E63] gap-2">
                        <span class="animate-spin text-2xl">⏳</span>
                        <p class="text-xs font-semibold">{{ __('Loading conversation logs...') }}</p>
                    </div>
                </template>

                <!-- Empty State -->
                <template x-if="!isLoading && messages.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center p-6">
                        <span class="text-4xl mb-2">💬</span>
                        <h4 class="font-bold text-[#3E2723] text-sm">{{ __('Start of Clinical Consult') }}</h4>
                        <p class="text-xs text-[#8D6E63] mt-1 max-w-xs leading-relaxed">
                            {{ __('Your conversation here is encrypted. All messages will be audited in compliance with medical confidentiality guidelines.') }}
                        </p>
                    </div>
                </template>

                <!-- Messages loop -->
                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.is_me ? 'justify-end' : 'justify-start'" class="flex w-full animate-fade-in">
                        <div :class="msg.is_me ? 'bg-[#3E2723] text-[#FFF8E1] rounded-br-none shadow-sm' : 'bg-[#EFEBE9]/60 text-[#3E2723] rounded-bl-none'" 
                             class="max-w-[75%] px-4 py-3 rounded-2xl flex flex-col gap-1.5">
                            
                            <!-- If file attachment exists -->
                            <template x-if="msg.file_path">
                                <div class="p-2.5 bg-[#5D4037]/20 rounded-xl border border-white/10 flex items-center justify-between gap-3 mb-1">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="text-base">📄</span>
                                        <div class="truncate text-left">
                                            <p class="text-[11px] font-bold truncate leading-tight" x-text="msg.file_name"></p>
                                            <p class="text-[8px] opacity-75 leading-tight" x-text="msg.file_type"></p>
                                        </div>
                                    </div>
                                    <a :href="msg.file_path" target="_blank" 
                                       class="px-2.5 py-1 bg-white text-[#3E2723] hover:bg-[#FDFBF7] text-[10px] font-bold rounded-lg transition shrink-0 shadow-sm">
                                        {{ __('Open') }}
                                    </a>
                                </div>
                            </template>

                            <p x-show="msg.message" class="text-xs font-medium leading-relaxed whitespace-pre-wrap" x-text="msg.message"></p>
                            <span :class="msg.is_me ? 'text-[#D7CCC8]' : 'text-[#8D6E63]'" class="text-[9px] font-bold text-right mt-0.5 block" x-text="msg.time"></span>
                        </div>
                    </div>
                </template>

            </div>

            <!-- Attached File Indicator -->
            <div x-show="attachedFile" x-cloak class="px-4 py-2 bg-[#FDFBF7] border-t border-[#D7CCC8]/40 flex items-center justify-between text-xs text-[#5D4037] border-b">
                <div class="flex items-center gap-2">
                    <span class="text-base">📎</span>
                    <span class="font-bold truncate" x-text="attachedFileName"></span>
                </div>
                <button @click="clearAttachment()" class="text-red-600 font-bold hover:text-red-800 text-[10px]">{{ __('Dismiss') }}</button>
            </div>

            <!-- Input Composition panel -->
            <div class="p-4 bg-white flex items-center gap-3">
                <input type="file" x-ref="fileInput" @change="handleFileChange($event)" class="hidden">
                
                <!-- Attachment Button -->
                <button @click="triggerFileSelect()" class="p-3 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[#5D4037] rounded-2xl transition shadow-sm flex items-center justify-center shrink-0" title="{{ __('Attach File') }}">
                    <span>📎</span>
                </button>

                <textarea x-model="newMessage"
                          @keydown.enter.prevent="sendMessage()"
                          rows="1"
                          placeholder="{{ __('Write a secure message...') }}"
                          class="flex-grow px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-2xl text-xs font-medium text-[#3E2723] transition placeholder-[#8D6E63]/60 resize-none max-h-24"></textarea>
                
                <button @click="sendMessage()"
                        :disabled="(newMessage.trim().length === 0 && !attachedFile) || isSending"
                        :class="((newMessage.trim().length === 0 && !attachedFile) || isSending) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#5D4037] active:scale-95'"
                        class="p-3 bg-[#3E2723] text-white rounded-2xl transition shadow-md flex items-center justify-center shrink-0">
                    <template x-if="!isSending">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </template>
                    <template x-if="isSending">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                </button>
            </div>

        </div>
    </div>
</x-app-layout>
