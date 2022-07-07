
@component('mail::message')
Olá **{{$name}}**,

Clique no botão abaixo para definir sua senha

@component('mail::button', ['url' => $link])
Ir para definição de senha
@endcomponent

@endcomponent
