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

            <!-- Контейнер для AJAX-уведомлений -->
            <div id="alert-container" class="mb-4"></div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" id="session-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" id="session-error">
                {{ session('error') }}
            </div>
            @endif

            <!-- Форма рассылки с AJAX -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📨 Отправить рассылку</h3>

                    <form id="broadcast-form" method="POST" action="{{ route('bots.broadcast', $bot->id) }}">
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
                                required>{{ old('message') }}</textarea>
                            <div id="message-error" class="text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div class="flex items-center">
                            <button type="submit"
                                id="submit-btn"
                                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                Отправить всем ({{ $bot->subscribers->count() }} подписчиков)
                            </button>

                            <!-- Спиннер загрузки (изначально скрыт) -->
                            <div id="loading-spinner" class="hidden ml-3">
                                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <!-- Список подписчиков -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">👥 Подписчики ({{ $bot->subscribers->count() }})</h3>

                    @if($bot->subscribers->count() > 0)
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
                                @foreach($bot->subscribers as $subscriber)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $subscriber->telegram_user_id ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $subscriber->first_name }} {{ $subscriber->last_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($subscriber->username)
                                        <a href="https://t.me/{{ $subscriber->username }}" target="_blank" class="text-blue-600 hover:underline">
                                            @ {{ $subscriber->username }}
                                        </a>
                                        @else
                                        <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $subscriber->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('subscribers.destroy', $subscriber) }}" class="inline delete-form">
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
                    @else
                    <p class="text-gray-500 text-center py-8">
                        Пока нет подписчиков. Попросите пользователей отправить боту команду /start
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔥 Скрипт загружен!');

            const form = document.getElementById('broadcast-form');
            const submitBtn = document.getElementById('submit-btn');
            const spinner = document.getElementById('loading-spinner');

            if (!form) {
                console.error('❌ Форма не найдена!');
                return;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📤 Форма отправлена');

                // Блокируем кнопку
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }

                // Показываем спиннер
                if (spinner) {
                    spinner.classList.remove('hidden');
                }

                // Получаем данные формы
                const formData = new FormData(form);

                // Отправляем AJAX запрос
                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('✅ Ответ:', data);

                        if (data.success) {
                            // Показываем успех
                            alert('✅ ' + data.message);
                            form.reset(); // Очищаем форму
                        } else {
                            alert('❌ ' + (data.message || 'Ошибка'));
                        }
                    })
                    .catch(error => {
                        console.error('❌ Ошибка:', error);
                        alert('❌ Ошибка соединения');
                    })
                    .finally(() => {
                        // Разблокируем кнопку и прячем спиннер
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                        if (spinner) {
                            spinner.classList.add('hidden');
                        }
                    });
            });
        });
    </script>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ JavaScript загружен!');

        const form = document.getElementById('broadcast-form');
        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('loading-spinner');

        if (!form) {
            console.error('❌ Форма не найдена!');
            return;
        }

        if (!submitBtn) {
            console.error('❌ Кнопка не найдена!');
            return;
        }

        if (!spinner) {
            console.error('❌ Спиннер не найдена!');
            return;
        }

        console.log('✅ Все элементы найдены:', {
            form,
            submitBtn,
            spinner
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('🚀 Форма отправлена!');

            // Меняем текст кнопки для наглядности
            submitBtn.textContent = 'Отправка...';
            submitBtn.disabled = true;

            // Показываем спиннер
            spinner.classList.remove('hidden');
            console.log('👀 Спиннер должен быть виден');

            // Имитация отправки (через 3 секунды вернем обратно)
            setTimeout(() => {
                submitBtn.textContent = 'Отправить всем ({{ $bot->subscribers->count() }} подписчиков)';
                submitBtn.disabled = false;
                spinner.classList.add('hidden');
                console.log('✅ Кнопка разблокирована');
            }, 3000);
        });
    });
</script>
@endpush