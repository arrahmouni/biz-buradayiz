<select @class(['form-select', 'form-select-solid' => $VALUE['solid'], 'select2-ajax' => $VALUE['isAjax'], $VALUE['class']])
    name="{{ $VALUE['name'] }}" id="{{ $VALUE['id'] }}" @if($VALUE['multiple']) multiple @endif
    data-placeholder="{{ $VALUE['placeholder'] }}"  data-allow-clear="{{ $VALUE['clearable'] }}"
    @if($VALUE['isAjax'])
        data-url="{{ $VALUE['url'] }}"
        data-selected="{{ json_encode($VALUE['selected']) }}"
        @if(!empty($VALUE['ajaxExtraData']))
            data-ajax-extra="{{ e(json_encode($VALUE['ajaxExtraData'])) }}"
        @endif
        @if(!empty($VALUE['parentSelect']))
            data-parent-select="{{ $VALUE['parentSelect'] }}"
        @endif
        @if(!empty($VALUE['ajaxParentParam']))
            data-ajax-parent-param="{{ $VALUE['ajaxParentParam'] }}"
        @endif
    @endif
    @if(!empty($VALUE['clearDependentsSelector']))
        data-clear-dependents="{{ $VALUE['clearDependentsSelector'] }}"
    @endif
    @if(!empty($VALUE['autoSelectFirst']))
        data-auto-select-first="true"
    @endif
    data-with-img="{{ $VALUE['withImg'] }}"
    @if($VALUE['select2']) data-control="select2" @endif data-dropdown-parent="{{ $VALUE['dropdownParent'] }}"
    data-hide-search="{{ $VALUE['searchable'] }}" @disabled($VALUE['disabled'])>

    @if(!empty($VALUE['placeholder']) && !$VALUE['multiple'])
        <option value></option>
    @endif

    @foreach ($VALUE['data'] as $key => $item)
        <option value="{{$VALUE['values']($key, $item)}}"
            @if($VALUE['select']($key, $item, $VALUE['value']))
                selected
            @endif>

            {{$VALUE['text']($key, $item)}}
        </option>
    @endforeach
</select>
