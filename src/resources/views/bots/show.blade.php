<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Управление ботом') }}: {{ $bot->name }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                ← Назад
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Форма рассылки -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📨 Отправить рассылку</h3>
                    
                    <form method="POST" action="{{ route('bots.broadcast', $bot) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                Сообщение (поддерживает Markdown)
                            </label>
                            <textarea 
                                name="message" 
                                id="message" 
                                rows="5"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Введите текст сообщения..."
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Отправить всем ({{ $bot->subscribers->count() }} подписчиков)
                        </button>
                    </form>
                </div>
            </div>

            <!-- Список подписчиков -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">👥 Подписчики</h3>
                    
                    @if($bot->subscribers->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Имя</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Подписался</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bot->subscribers as $subscriber)
                                <tr>
                                    <td class="px-6 py-4">{{ $subscriber->telegram_user_id ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $subscriber->first_name }} {{ $subscriber->last_name }}</td>
                                    <td class="px-6 py-4">
                                        @if($subscriber->username)
                                            @<a href="https://t.me/{{ $subscriber->username }}" target="_blank" class="text-blue-600">
                                                {{ $subscriber->username }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ $subscriber->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('subscribers.destroy', $subscriber) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Удалить подписчика?')">
                                                Удалить
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 text-center py-4">
                            Пока нет подписчиков. Попросите пользователей отправить боту команду /start
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>