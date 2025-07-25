<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConviteCadastroMail extends Mailable
{
    use Queueable, SerializesModels;

    public $remetente;
    public $emailConvidado;
    public $linkCadastro;

    /**
     * Create a new message instance.
     */
    public function __construct(Usuario $remetente, $emailConvidado)
    {
        $this->remetente = $remetente;
        $this->emailConvidado = $emailConvidado;
        $this->linkCadastro = url('/cadastro?email=' . urlencode($emailConvidado) . '&convite=' . base64_encode($remetente->id));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->remetente->name . ' convidou você para o Relatópia!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.convite-cadastro',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
