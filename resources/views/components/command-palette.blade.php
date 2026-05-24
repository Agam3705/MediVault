<div x-data="{
        isOpen: false,
        searchQuery: '',
        results: [],
        isLoading: false,
        selectedIndex: -1,
        
        async fetchResults() {
            if (this.searchQuery.trim().length === 0) {
                this.results = [];
                return;
            }
            this.isLoading = true;
            try {
                const res = await fetch(`/search?q=${encodeURIComponent(this.searchQuery)}`);
                this.results = await res.json();
                this.selectedIndex = this.results.length > 0 ? 0 : -1;
            } catch(e) {
                console.error(e);
            } finally {
                this.isLoading = false;
            }
        },

        navigate() {
            if (this.selectedIndex >= 0 && this.selectedIndex < this.results.length) {
                window.location.href = this.results[this.selectedIndex].url;
            }
        }
     }"
     @keydown.window.prevent.ctrl.k="isOpen = !isOpen"
     @keydown.window.prevent.cmd.k="isOpen = !isOpen"
     @keydown.window.escape="isOpen = false"
     @open-search.window="isOpen = !isOpen"
     x-cloak
     class="relative z-50">

    <!-- Backdrop overlay -->
    <div x-show="isOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#3E2723]/40 backdrop-blur-sm transition-opacity"
         @click="isOpen = false"></div>

    <!-- Palette Modal Panel -->
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="isOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20 flex justify-center">
        
        <div @click.stop class="mx-auto w-full max-w-2xl transform divide-y divide-[#D7CCC8]/30 overflow-hidden rounded-3xl bg-white border border-[#D7CCC8]/60 shadow-2xl transition-all h-fit mt-12">
            <!-- Search bar input -->
            <div class="relative">
                <span class="absolute inset-y-0 left-4 flex items-center text-lg pointer-events-none">🔍</span>
                <input type="text" 
                       x-model="searchQuery"
                       @input.debounce.250ms="fetchResults()"
                       @keydown.arrow-down.prevent="selectedIndex = (selectedIndex + 1) % results.length"
                       @keydown.arrow-up.prevent="selectedIndex = (selectedIndex - 1 + results.length) % results.length"
                       @keydown.enter.prevent="navigate()"
                       placeholder="Search medical records, actions, patients, doctor grants..."
                       class="h-14 w-full border-0 bg-transparent pl-12 pr-4 text-[#3E2723] focus:ring-0 focus:outline-none placeholder-[#8D6E63]/60 text-sm font-medium">
                
                <span class="absolute inset-y-0 right-4 flex items-center text-[10px] font-bold text-[#8D6E63] bg-[#EFEBE9] px-2 py-0.5 rounded-md my-auto h-fit border border-[#D7CCC8]/40">ESC</span>
            </div>

            <!-- Results dropdown -->
            <div x-show="results.length > 0 || isLoading || searchQuery.length > 0" class="max-h-80 scroll-py-2 overflow-y-auto p-2">
                <template x-if="isLoading">
                    <div class="flex items-center justify-center py-6 text-[#8D6E63] text-xs font-semibold gap-2">
                        <span class="animate-spin text-lg">⏳</span> Searching records database...
                    </div>
                </template>

                <template x-if="!isLoading && results.length === 0 && searchQuery.length > 0">
                    <div class="py-6 text-center text-[#8D6E63] text-xs font-semibold">
                        No matches found for "<span class="text-[#3E2723]" x-text="searchQuery"></span>".
                    </div>
                </template>

                <ul class="text-sm text-gray-700 divide-y divide-[#D7CCC8]/10" x-show="results.length > 0">
                    <template x-for="(item, idx) in results" :key="idx">
                        <li :class="idx === selectedIndex ? 'bg-[#FDFBF7] border-l-4 border-[#3E2723]' : 'hover:bg-[#FDFBF7]/50 border-l-4 border-transparent'"
                            class="group flex cursor-pointer select-none items-center justify-between p-3 transition"
                            @click="selectedIndex = idx; navigate()">
                            
                            <div class="flex items-center gap-3">
                                <span class="text-lg" x-text="item.icon"></span>
                                <div>
                                    <div class="font-bold text-xs text-[#3E2723]" x-text="item.title"></div>
                                    <div class="text-[10px] text-[#8D6E63] mt-0.5" x-text="item.detail"></div>
                                </div>
                            </div>
                            
                            <span class="text-[10px] font-bold text-[#8D6E63] bg-[#EFEBE9] px-2 py-0.5 rounded-full uppercase" x-text="item.category"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Footer hint -->
            <div class="flex items-center justify-between bg-[#FDFBF7] px-4 py-2.5 text-[10px] text-[#8D6E63] font-semibold">
                <div class="flex gap-3">
                    <span><kbd class="bg-white border border-[#D7CCC8]/60 px-1 rounded shadow-sm">↑↓</kbd> Navigate</span>
                    <span><kbd class="bg-white border border-[#D7CCC8]/60 px-1 rounded shadow-sm">Enter</kbd> Select</span>
                </div>
                <span>MediVault Command Panel</span>
            </div>
        </div>
    </div>
</div>
