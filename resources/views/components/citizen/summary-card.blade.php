<!--
    Este componente exibe um cartão de resumo com um valor e uma label.
-->
<article class="summary-card {{ $type ?? '' }}">

    <strong>{{ $value }}</strong>

    <span>{{ $label }}</span>

</article>