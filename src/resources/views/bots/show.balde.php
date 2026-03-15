<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Управление ботом') }}: {{ $bot->name }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                ← Назад к списку
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Информация о боте -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2">🤖 Информация о боте</h3>
                    <p><strong>Название:</strong> {{ $bot->name }}</p>
                    <p><strong>Токен:</strong> {{ substr($bot->token, 0, 30) }}...</p>
                    <p><strong>Дата добавления:</strong> {{ $bot->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>

            <!-- Форма рассылки -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📨 Отправить рассылку</h3>
                    
                    <form method="POST" action="{{ route('bots.broadcast', $bot) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Сообщение</label>
                            <textarea 
                                name="message" 
                                id="message" 
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Введите текст сообщения для подписчиков..."
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" 
                            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                            Отправить всем ({{ $bot->subscribers->count() }} подписчиков)
                        </button>
                    </form>
                </div>
            </div>

            <!-- Список подписчиков -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">👥 Подписчики ({{ $subscribers->total() }})</h3>
                    
                    @if($subscribers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Имя</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Подписался</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subscribers as $subscriber)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subscriber->telegram_user_id ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subscriber->first_name }} {{ $subscriber->last_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($subscriber->username)
                                                @<a href="https://t.me/{{ $subscriber->username }}" target="_blank" class="text-blue-600 hover:underline">
                                                    {{ $subscriber->username }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">нет</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subscriber->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form method="POST" action="{{ route('subscribers.destroy', $subscriber) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Удалить подписчика?')">
                                                    Удалить
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Пагинация -->
                        <div class="mt-4">
                            {{ $subscribers->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            Пока нет подписчиков. Попросите пользователей написать боту команду /start
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>