<?php
// Configure por variáveis de ambiente do Apache/WampServer, sem publicar senhas.
return [
 'usuario' => getenv('IFFAR_SMTP_USER') ?: '',
 'senha' => getenv('IFFAR_SMTP_PASS') ?: '',
 'remetente' => getenv('IFFAR_SMTP_USER') ?: ''
];
