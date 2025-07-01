<x-dashboard-layout>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Sentimentos do parceiro</h1>

        @if($relacionamento->permissoes->where('permissao', 'ver_sentimentos')->first()?->valor)
            <ul class="space-y-4">
                @forelse($sentimentos as $sentimento)
                    <li class="bg-white rounded-lg shadow p-4">
                        <div class="font-semibold">{{ ucfirst($sentimento->tipo_sentimento) }}</div>
                        <div class="text-gray-600 text-sm">{{ $sentimento->created_at->format('d/m/Y H:i') }}</div>
                        <div class="mt-2">{{ $sentimento->descricao }}</div>
                    </li>
                @empty
                    <li class="text-gray-500">Nenhum sentimento registrado.</li>
                @endforelse
            </ul>
        @else
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg text-center">
                Você não tem permissão para visualizar os sentimentos deste parceiro.
            </div>
        @endif
    </div>
</x-dashboard-layout>
