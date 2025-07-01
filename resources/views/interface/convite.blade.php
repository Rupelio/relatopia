<x-dashboard-layout>
    @if(!auth()->check())
        <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded-lg text-center">
            Para aceitar ou recusar o convite, faça login na sua conta.
            <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="underline text-emerald-700 font-semibold ml-1">Entrar</a>
        </div>
    @endif
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 bg-gray-50">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-emerald-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-6 text-center">
                <h1 class="text-2xl font-bold text-white">Convite de Relacionamento</h1>
                <p class="text-emerald-100 mt-1">Aceite ou recuse o convite para vincular contas</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="p-3 bg-pink-100 rounded-lg">
                            <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">
                        {{ $relacionamento->usuario1->nome ?? $relacionamento->usuario1->email }} te convidou para vincular contas!
                    </h2>
                    <p class="text-gray-600 mb-4">
                        Ao aceitar, vocês poderão compartilhar informações conforme as permissões definidas.
                    </p>
                </div>

                <form method="POST" action="{{ url('/relacionamento/convite/'.$relacionamento->token.'/aceitar') }}" class="space-y-4">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold shadow-md hover:bg-emerald-700 transition-all duration-200" {{ !auth()->check() ? 'disabled opacity-50 cursor-not-allowed' : '' }}>
                        Aceitar Convite
                    </button>
                </form>
                <form method="POST" action="{{ url('/relacionamento/convite/'.$relacionamento->token.'/recusar') }}">
                    @csrf
                    <button type="submit" class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-200" {{ !auth()->check() ? 'disabled opacity-50 cursor-not-allowed' : '' }}>
                        Recusar Convite
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>
