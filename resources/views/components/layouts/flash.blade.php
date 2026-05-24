<!-- Flash Toast Component -->
<div x-data="{ show: false, message: '', type: 'success' }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="fixed bottom-4 right-4 max-w-xs w-full z-50">
  <div :class="{'bg-green-100 text-green-800 border-green-200': type === 'success', 'bg-red-100 text-red-800 border-red-200': type === 'error', 'bg-blue-100 text-blue-800 border-blue-200': type === 'info'}" class="border-l-4 p-4 rounded-lg shadow-lg flex items-start space-x-3">
    <template x-if="type === 'success'">
      <svg class="flex-shrink-0 h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 11.586 7.707 10.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    </template>
    <template x-if="type === 'error'">
      <svg class="flex-shrink-0 h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 01-2 0v-4zm1-5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"/></svg>
    </template>
    <template x-if="type === 'info'">
      <svg class="flex-shrink-0 h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 9h2V7H9v2zm0 4h2v-2H9v2zm1-9a7 7 0 100 14A7 7 0 0010 4z"/></svg>
    </template>
    <div class="flex-1">
      <p x-text="message" class="text-sm"></p>
    </div>
    <button @click="show = false" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
  </div>
</div>
