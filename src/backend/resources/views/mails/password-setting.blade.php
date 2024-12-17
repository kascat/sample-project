@component('mail::message')
    Olá **{{$name}}**,

    Acesse agora, clique no botão abaixo e defina sua senha

    @component('mail::button', ['url' => $link])
        Ir para definição de senha
    @endcomponent
@endcomponent
