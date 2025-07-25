<?php

namespace App\Http\Controllers;

use App\Models\ListaDesejo;
use App\Models\Relacionamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ListaDesejoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $itens = $user->listaDesejos()
            ->orderBy('comprado', 'asc')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($itens->map(function ($item) {
            return [
                'id' => $item->id,
                'titulo' => $item->titulo,
                'descricao' => $item->descricao,
                'link_compra' => $item->link_compra,
                'preco_estimado' => $item->preco_estimado,
                'preco_formatado' => $item->preco_formatado,
                'prioridade' => $item->prioridade,
                'prioridade_color' => $item->prioridade_color,
                'prioridade_icon' => $item->prioridade_icon,
                'comprado' => $item->comprado,
                'comprado_por' => $item->compradorPor?->name,
                'data_compra' => $item->data_compra?->format('d/m/Y H:i'),
                'observacoes' => $item->observacoes,
                'created_at' => $item->created_at->format('d/m/Y')
            ];
        }));
    }

    public function indexParceiro($relacionamento)
    {
        $user = Auth::user();

        // Verificar se o usuário tem acesso ao relacionamento
        $rel = Relacionamento::where('id', $relacionamento)
            ->where(function($query) use ($user) {
                $query->where('user_id_1', $user->id)
                      ->orWhere('user_id_2', $user->id);
            })
            ->firstOrFail();

        // Buscar o parceiro
        $parceiroId = $rel->user_id_1 === $user->id
            ? $rel->user_id_2
            : $rel->user_id_1;

        $itens = ListaDesejo::where('usuario_id', $parceiroId)
            ->orderBy('comprado', 'asc')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($itens->map(function ($item) {
            return [
                'id' => $item->id,
                'titulo' => $item->titulo,
                'descricao' => $item->descricao,
                'link_compra' => $item->link_compra,
                'preco_estimado' => $item->preco_estimado,
                'preco_formatado' => $item->preco_formatado,
                'prioridade' => $item->prioridade,
                'prioridade_color' => $item->prioridade_color,
                'prioridade_icon' => $item->prioridade_icon,
                'comprado' => $item->comprado,
                'comprado_por' => $item->compradorPor?->name,
                'data_compra' => $item->data_compra?->format('d/m/Y H:i'),
                'observacoes' => $item->observacoes,
                'created_at' => $item->created_at->format('d/m/Y'),
                'pode_comprar' => !$item->comprado // Só pode comprar se não foi comprado ainda
            ];
        }));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'link_compra' => 'nullable|url|max:500',
            'preco_estimado' => 'nullable|numeric|min:0|max:999999.99',
            'prioridade' => 'required|in:baixa,media,alta'
        ]);

        $item = ListaDesejo::create([
            'usuario_id' => $user->id,
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'],
            'link_compra' => $validated['link_compra'],
            'preco_estimado' => $validated['preco_estimado'],
            'prioridade' => $validated['prioridade']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item adicionado à lista de desejos!',
            'item' => [
                'id' => $item->id,
                'titulo' => $item->titulo,
                'descricao' => $item->descricao,
                'link_compra' => $item->link_compra,
                'preco_formatado' => $item->preco_formatado,
                'prioridade' => $item->prioridade,
                'prioridade_color' => $item->prioridade_color,
                'prioridade_icon' => $item->prioridade_icon,
                'comprado' => false,
                'created_at' => $item->created_at->format('d/m/Y')
            ]
        ], 201);
    }

    public function marcarComoComprado(Request $request, $itemId)
    {
        $user = Auth::user();

        $item = ListaDesejo::findOrFail($itemId);

        // Verificar se o item já foi comprado
        if ($item->comprado) {
            throw ValidationException::withMessages([
                'item' => 'Este item já foi marcado como comprado.'
            ]);
        }

        // Permitir que tanto o dono do item quanto o parceiro possam marcar como comprado
        // Verificar se o usuário tem permissão (deve ser o dono do item OU o parceiro)
        $temPermissao = false;

        if ($item->usuario_id === $user->id) {
            // É o próprio dono do item - pode marcar como comprado
            $temPermissao = true;
        } else {
            // Verificar se é o parceiro através do relacionamento
            $relacionamento = \App\Models\Relacionamento::where(function($query) use ($user, $item) {
                $query->where(function($subQuery) use ($user, $item) {
                    $subQuery->where('user_id_1', $user->id)
                             ->where('user_id_2', $item->usuario_id);
                })->orWhere(function($subQuery) use ($user, $item) {
                    $subQuery->where('user_id_1', $item->usuario_id)
                             ->where('user_id_2', $user->id);
                });
            })->where('status', 'ativo')->first();

            if ($relacionamento) {
                $temPermissao = true;
            }
        }

        if (!$temPermissao) {
            throw ValidationException::withMessages([
                'item' => 'Você não tem permissão para marcar este item como comprado.'
            ]);
        }

        $observacoes = $request->input('observacoes');

        $item->update([
            'comprado' => true,
            'comprado_por' => $user->id,
            'data_compra' => now(),
            'observacoes' => $observacoes
        ]);

        $quemComprou = $item->usuario_id === $user->id ? 'Você mesmo' : $user->name;

        return response()->json([
            'success' => true,
            'message' => 'Item marcado como comprado!',
            'item' => [
                'id' => $item->id,
                'comprado' => true,
                'comprado_por' => $quemComprou,
                'data_compra' => $item->data_compra->format('d/m/Y H:i'),
                'observacoes' => $item->observacoes
            ]
        ]);
    }

    public function destroy($itemId)
    {
        $user = Auth::user();

        $item = ListaDesejo::where('id', $itemId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        // Não permitir exclusão se já foi comprado
        if ($item->comprado) {
            throw ValidationException::withMessages([
                'item' => 'Não é possível remover itens que já foram comprados.'
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removido da lista de desejos!'
        ]);
    }

    public function estatisticas()
    {
        $user = Auth::user();

        $total = $user->listaDesejos()->count();
        $comprados = $user->listaDesejos()->where('comprado', true)->count();
        $pendentes = $total - $comprados;
        $valorTotal = $user->listaDesejos()->sum('preco_estimado');
        $valorComprados = $user->listaDesejos()->where('comprado', true)->sum('preco_estimado');

        return response()->json([
            'total' => $total,
            'comprados' => $comprados,
            'pendentes' => $pendentes,
            'valor_total' => $valorTotal,
            'valor_comprados' => $valorComprados,
            'valor_total_formatado' => $valorTotal ? 'R$ ' . number_format($valorTotal, 2, ',', '.') : 'R$ 0,00',
            'valor_comprados_formatado' => $valorComprados ? 'R$ ' . number_format($valorComprados, 2, ',', '.') : 'R$ 0,00'
        ]);
    }

    public function estatisticasParceiro($relacionamento)
    {
        $user = Auth::user();

        // Verificar se o usuário tem acesso ao relacionamento
        $rel = Relacionamento::where('id', $relacionamento)
            ->where(function($query) use ($user) {
                $query->where('user_id_1', $user->id)
                      ->orWhere('user_id_2', $user->id);
            })
            ->firstOrFail();

        // Buscar o parceiro
        $parceiroId = $rel->user_id_1 === $user->id
            ? $rel->user_id_2
            : $rel->user_id_1;

        $total = ListaDesejo::where('usuario_id', $parceiroId)->count();
        $comprados = ListaDesejo::where('usuario_id', $parceiroId)->where('comprado', true)->count();
        $pendentes = $total - $comprados;
        $valorTotal = ListaDesejo::where('usuario_id', $parceiroId)->sum('preco_estimado');
        $valorComprados = ListaDesejo::where('usuario_id', $parceiroId)->where('comprado', true)->sum('preco_estimado');

        return response()->json([
            'total' => $total,
            'comprados' => $comprados,
            'pendentes' => $pendentes,
            'valor_total' => $valorTotal,
            'valor_comprados' => $valorComprados,
            'valor_total_formatado' => $valorTotal ? 'R$ ' . number_format($valorTotal, 2, ',', '.') : 'R$ 0,00',
            'valor_comprados_formatado' => $valorComprados ? 'R$ ' . number_format($valorComprados, 2, ',', '.') : 'R$ 0,00'
        ]);
    }
}
