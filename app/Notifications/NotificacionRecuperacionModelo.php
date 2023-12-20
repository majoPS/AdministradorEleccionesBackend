<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionRecuperacionModelo extends Notification
{
    use Queueable;

    public $nombre;
    public $usuario;
    public $contrasena;

    /**
     * Create a new notification instance.
     *
     * @param string $nombre
     * @param string $usuario
     * @param string $contrasena
     * @return void
     */
    public function __construct($nombre, $usuario, $contrasena)
    {
        $this->nombre = $nombre;
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from('TEU@umss.edu', 'Tribunal Electoral Universitario UMSS')
            ->subject("Notificación")
            ->greeting("Sistema de gestión de elecciones")
            ->line("Hemos recibido una solicitud para recuperar tu información de usuario y contraseña.")
            ->line("Usuario: " . $this->usuario)
            ->line("Contraseña: " . $this->contrasena)
            ->line("¡Gracias!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
