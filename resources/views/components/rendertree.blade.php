@props(['tree', 'namecheckbox'])

<ul id="treeView">

@foreach ($tree as $key => $subTree)
    @isset($subTree['?'])
        <li><input type='checkbox' value="{{ $subTree['?'] }}|{{ $subTree['*'] }}" name="{{ $namecheckbox }}" />{{ $key }}</li>
    @else
        <li><details><summary>{{ $key }}</summary><x-rendertree :tree="$subTree" :namecheckbox="$namecheckbox" /></details>
    @endisset
@endforeach

</ul>
