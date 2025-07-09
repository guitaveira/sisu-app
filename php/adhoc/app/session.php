<?php

function getSessionId(): string
{
    // Garante que a sessão foi inicializada.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return session_id();
}