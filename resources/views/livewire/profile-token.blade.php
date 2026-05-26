
<div class="my-6 p-4 border rounded bg-gray-50 dark:bg-gray-800">
    <button wire:click="generateToken" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Generar Token
    </button>

    @if($token)
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Token generado:</label>
            <textarea readonly class="w-full p-2 border rounded bg-gray-100 dark:bg-gray-900">{{ $token }}</textarea>
        </div>
    @endif
</div>
