<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Сообщения об успехе/ошибке -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Форма добавления бота -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">➕ Добавить нового бота</h3>
                    
                    <form method="POST" action="{{ route('bots.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Название бота</label>
                            <input type="text" name="name" id="name" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Мой первый бот"
                                value="{{ old('name') }}"
                                required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="token" class="block text-sm font-medium text-gray-700">Токен бота (от @BotFather)</label>
                            <input type="text" name="token" id="token" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz"
                                value="{{ old('token') }}"
                                required>
                            @error('token')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Добавить бота
                        </button>
                    </form>
                </div>
            </div>

            <!-- Список ботов -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">📋 Мои боты</h3>
                    
                    @if($bots->count() > 0)
                        <div class="space-y-4">
                            @foreach($bots as $bot)
                                <div class="border rounded-lg p-4 flex justify-between items-center hover:bg-gray-50">
                                    <div>
                                        <h4 class="font-medium">{{ $bot->name }}</h4>
                                        <p class="text-sm text-gray-600">
                                            Токен: {{ substr($bot->token, 0, 20) }}...
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Подписчиков: {{ $bot->subscribers()->count() }}
                                        </p>
                                    </div>
                                    <div class="space-x-2">
                                        <a href="{{ route('bots.show', $bot->id) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            Управление
                                        </a>
                                        <form method="POST" action="{{ route('bots.destroy', $bot->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Удалить бота? Все подписчики также будут удалены.')">
                                                Удалить
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">
                            У вас пока нет добавленных ботов. Создайте бота у @BotFather и добавьте его выше.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>